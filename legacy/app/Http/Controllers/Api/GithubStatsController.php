<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GithubStatsController extends Controller
{
    private string $username;

    private ?string $token;

    public function __construct()
    {
        $this->username = config('services.github.username', 'cdrc1280');
        $this->token = config('services.github.token');
    }

    public function stats(): JsonResponse
    {
        $data = Cache::remember("github-stats:{$this->username}", now()->addMinutes(30), function () {
            return [
                'username' => $this->username,
                'repos' => $this->fetchRepoStats(),
                'contributions' => $this->fetchContributionCalendar(),
                // Deliberately public-only — see fetchLatestCommits() docblock.
                'latest_commits' => $this->fetchLatestCommits(),
            ];
        });

        return response()->json($data);
    }

    public function refresh(): JsonResponse
    {
        Cache::forget("github-stats:{$this->username}");

        return $this->stats();
    }

    private function githubRest(string $path)
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->acceptJson()
                ->timeout(5)
                ->get("https://api.github.com{$path}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::info("GitHub REST API returned status {$response->status()} for {$path}", [
                'body' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::warning("GitHub REST call exception for {$path}: ".$e->getMessage());
        }

        return null;
    }

    private function authHeaders(): array
    {
        $headers = ['User-Agent' => 'portfolio-site'];
        if ($this->token) {
            $headers['Authorization'] = "Bearer {$this->token}";
        }

        return $headers;
    }

    /**
     * Stars, forks, and language distribution across every repo you're affiliated
     * with — owned, collaborator, or org-member — public and private, via the
     * authenticated /user/repos endpoint.
     *
     * IMPORTANT: only aggregates leave this function — no repo name, no repo URL,
     * no description ever gets included in the response. That's what makes it safe
     * to include private/collaborator repos here even though this endpoint is
     * public-facing — a stat like "30% Vue.js" doesn't reveal which project it
     * came from.
     *
     * Requires a token with access to private repos (see note in
     * fetchContributionCalendar) AND, for collaborator repos specifically,
     * requires that access to actually be granted — see the token note below.
     */
    private function fetchRepoStats(): array
    {
        $repos = $this->githubRest('/user/repos?visibility=all&affiliation=owner,collaborator,organization_member&per_page=100');

        if (! is_array($repos)) {
            return [
                'total_stars' => null,
                'total_forks' => null,
                'top_languages' => [],
                'available' => false,
            ];
        }

        $totalStars = 0;
        $totalForks = 0;
        $languageCounts = [];

        foreach ($repos as $repo) {
            $totalStars += $repo['stargazers_count'] ?? 0;
            $totalForks += $repo['forks_count'] ?? 0;

            if (! empty($repo['language'])) {
                $languageCounts[$repo['language']] = ($languageCounts[$repo['language']] ?? 0) + 1;
            }
        }

        $totalRepoCount = array_sum($languageCounts) ?: 1;
        arsort($languageCounts);

        $colors = [
            'PHP' => '#4F5D95',
            'Vue' => '#42b883',
            'JavaScript' => '#f1e05a',
            'TypeScript' => '#3178c6',
            'HTML' => '#e34f26',
            'CSS' => '#563d7c',
            'Blade' => '#f7523f',
            'Python' => '#3572A5',
        ];

        $topLanguages = [];
        foreach (array_slice($languageCounts, 0, 4, true) as $lang => $count) {
            $topLanguages[] = [
                'name' => $lang,
                'percentage' => round(($count / $totalRepoCount) * 100),
                'color' => $colors[$lang] ?? '#8b8b8b',
            ];
        }

        return [
            'total_stars' => $totalStars,
            'total_forks' => $totalForks,
            'top_languages' => $topLanguages,
            'available' => true,
        ];
    }

    /**
     * Full contribution total including private repos — matches what your GitHub
     * profile page shows (e.g. "1,843 contributions"), not just public activity.
     *
     * To get the private counts included, your token needs read access to private
     * repos:
     *   - Fine-grained PAT: set Repository access to "All repositories" (not
     *     "Public Repositories only"), with Contents: Read-only.
     *   - Classic PAT: the `repo` scope (full private repo read access) is what
     *     GitHub's GraphQL API checks for private contribution counting.
     * With a public-only token this will keep returning a much lower number than
     * your real profile, the same undercount you just saw.
     */
    private function fetchContributionCalendar(): array
    {
        if (! $this->token) {
            return ['total' => null, 'weeks' => [], 'available' => false];
        }

        $query = <<<'GQL'
        query($login: String!) {
          user(login: $login) {
            contributionsCollection {
              contributionCalendar {
                totalContributions
                weeks {
                  contributionDays {
                    contributionCount
                    date
                  }
                }
              }
            }
          }
        }
        GQL;

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->timeout(5)
                ->post('https://api.github.com/graphql', [
                    'query' => $query,
                    'variables' => ['login' => $this->username],
                ]);

            if ($response->successful()) {
                $calendar = $response->json('data.user.contributionsCollection.contributionCalendar');
                if (! empty($calendar['weeks'])) {
                    return [
                        'total' => $calendar['totalContributions'],
                        'weeks' => $calendar['weeks'],
                        'available' => true,
                    ];
                }

                Log::info('GitHub GraphQL succeeded but returned no calendar data', [
                    'response' => $response->json(),
                ]);
            } else {
                Log::info('GitHub GraphQL contribution call failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('GitHub GraphQL contribution exception: '.$e->getMessage());
        }

        return ['total' => null, 'weeks' => [], 'available' => false];
    }

    /**
     * Deliberately PUBLIC repos only, even though the token can see more.
     *
     * Unlike the aggregate repo stats above, this panel displays actual repo
     * names and commit messages verbatim. Some of your private repos are
     * government systems (TESDA) — surfacing their commit history on a public
     * portfolio page is a confidentiality concern, not just a cosmetic one, so
     * this intentionally stays scoped to /events/public regardless of what the
     * token is capable of fetching.
     *
     * If you want private-repo commits shown here too, that's a call to make
     * deliberately per-repo (e.g. only your personal side projects), not a
     * blanket switch to /events.
     */
    private function fetchLatestCommits(): array
    {
        $events = $this->githubRest("/users/{$this->username}/events/public?per_page=30");

        if (! is_array($events)) {
            return [];
        }

        $commits = [];
        foreach ($events as $event) {
            if (($event['type'] ?? null) !== 'PushEvent' || empty($event['payload']['commits'])) {
                continue;
            }

            $lastCommit = end($event['payload']['commits']);

            $commits[] = [
                'repo' => $event['repo']['name'],
                'message' => explode("\n", $lastCommit['message'])[0],
                'time' => Carbon::parse($event['created_at'])->diffForHumans(),
            ];

            if (count($commits) >= 3) {
                break;
            }
        }

        return $commits;
    }
}
