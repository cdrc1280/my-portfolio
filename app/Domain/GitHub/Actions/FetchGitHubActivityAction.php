<?php

declare(strict_types=1);

namespace App\Domain\GitHub\Actions;

use App\Domain\GitHub\DataTransferObjects\GitHubActivityData;
use App\Domain\GitHub\Support\GitHubClient;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Fetches a fresh activity summary from GitHub (uncached). Returns null
 * when GitHub is unreachable or the user does not exist.
 */
final class FetchGitHubActivityAction
{
    public function __construct(
        private readonly GitHubClient $client,
    ) {}

    public function execute(string $username): ?GitHubActivityData
    {
        try {
            $user = $this->client->request()->get("/users/{$username}");
            $repos = $this->client->request()->get("/users/{$username}/repos", [
                'type' => 'owner',
                'sort' => 'pushed',
                'per_page' => 100,
            ]);
        } catch (Throwable $exception) {
            Log::warning('GitHub activity fetch failed.', ['error' => $exception->getMessage()]);

            return null;
        }

        if (! $user->successful() || ! $repos->successful()) {
            Log::info('GitHub API returned an error.', ['user' => $user->status(), 'repos' => $repos->status()]);

            return null;
        }

        /** @var list<array<string, mixed>> $repoList */
        $repoList = array_values(array_filter(
            (array) $repos->json(),
            fn (mixed $repo): bool => is_array($repo) && ! ($repo['fork'] ?? false) && ! ($repo['private'] ?? false),
        ));

        return new GitHubActivityData(
            username: (string) $user->json('login', $username),
            profileUrl: (string) $user->json('html_url', "https://github.com/{$username}"),
            publicRepos: (int) $user->json('public_repos', 0),
            followers: (int) $user->json('followers', 0),
            totalStars: array_sum(array_map(fn (array $repo): int => (int) ($repo['stargazers_count'] ?? 0), $repoList)),
            contributionsLastYear: $this->contributions($username),
            languages: $this->languages($repoList),
            recentRepos: $this->recent($repoList),
        );
    }

    /**
     * Share of repositories per primary language, top five.
     *
     * @param  list<array<string, mixed>>  $repos
     * @return list<array{name: string, share: int}>
     */
    private function languages(array $repos): array
    {
        $counts = [];
        foreach ($repos as $repo) {
            if (is_string($repo['language'] ?? null)) {
                $counts[$repo['language']] = ($counts[$repo['language']] ?? 0) + 1;
            }
        }

        arsort($counts);
        $total = array_sum($counts) ?: 1;

        $languages = [];
        foreach (array_slice($counts, 0, 5, true) as $name => $count) {
            $languages[] = ['name' => (string) $name, 'share' => (int) round($count / $total * 100)];
        }

        return $languages;
    }

    /**
     * @param  list<array<string, mixed>>  $repos
     * @return list<array{name: string, url: string, description: string|null, language: string|null, stars: int, pushed_at: string}>
     */
    private function recent(array $repos): array
    {
        return array_map(fn (array $repo): array => [
            'name' => (string) ($repo['name'] ?? ''),
            'url' => (string) ($repo['html_url'] ?? ''),
            'description' => is_string($repo['description'] ?? null) ? $repo['description'] : null,
            'language' => is_string($repo['language'] ?? null) ? $repo['language'] : null,
            'stars' => (int) ($repo['stargazers_count'] ?? 0),
            'pushed_at' => (string) ($repo['pushed_at'] ?? ''),
        ], array_slice($repos, 0, 3));
    }

    /**
     * Public contribution count for the last year (GraphQL needs a token).
     */
    private function contributions(string $username): ?int
    {
        if (! $this->client->hasToken()) {
            return null;
        }

        $response = $this->client->request()->post('/graphql', [
            'query' => 'query($login: String!) { user(login: $login) { contributionsCollection { contributionCalendar { totalContributions } } } }',
            'variables' => ['login' => $username],
        ]);

        $total = $response->json('data.user.contributionsCollection.contributionCalendar.totalContributions');

        return is_int($total) ? $total : null;
    }
}
