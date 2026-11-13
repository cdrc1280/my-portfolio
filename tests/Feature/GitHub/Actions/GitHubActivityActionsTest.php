<?php

use App\Domains\GitHub\Actions\FetchGitHubActivityAction;
use App\Domains\GitHub\Actions\GetGitHubActivityAction;
use Illuminate\Support\Facades\Http;

function fakeGitHub(int $status = 200): void
{
    Http::fake([
        'api.github.com/users/octo' => Http::response(['login' => 'octo', 'html_url' => 'https://github.com/octo', 'public_repos' => 3, 'followers' => 7], $status),
        'api.github.com/users/octo/repos*' => Http::response([
            ['name' => 'a', 'html_url' => 'https://github.com/octo/a', 'language' => 'PHP', 'stargazers_count' => 5, 'fork' => false, 'private' => false, 'pushed_at' => '2026-11-01T00:00:00Z'],
            ['name' => 'b', 'html_url' => 'https://github.com/octo/b', 'language' => 'PHP', 'stargazers_count' => 1, 'fork' => false, 'private' => false, 'pushed_at' => '2026-10-01T00:00:00Z'],
            ['name' => 'c', 'html_url' => 'https://github.com/octo/c', 'language' => 'TypeScript', 'stargazers_count' => 2, 'fork' => false, 'private' => false, 'pushed_at' => '2026-09-01T00:00:00Z'],
            ['name' => 'fork', 'html_url' => 'https://github.com/octo/fork', 'language' => 'Go', 'stargazers_count' => 99, 'fork' => true, 'private' => false, 'pushed_at' => '2026-11-02T00:00:00Z'],
        ], $status),
    ]);
}

beforeEach(fn () => config(['portfolio.github.username' => 'octo', 'portfolio.github.token' => null]));

test('summarizes public, non-fork repositories', function () {
    fakeGitHub();

    $activity = app(FetchGitHubActivityAction::class)->execute('octo');

    expect($activity)
        ->totalStars->toBe(8)
        ->publicRepos->toBe(3)
        ->contributionsLastYear->toBeNull()
        ->languages->toBe([['name' => 'PHP', 'share' => 67], ['name' => 'TypeScript', 'share' => 33]])
        ->and(array_column($activity->recentRepos, 'name'))->toBe(['a', 'b', 'c']);
});

test('returns null when GitHub errors', function () {
    fakeGitHub(500);

    expect(app(FetchGitHubActivityAction::class)->execute('octo'))->toBeNull();
});

test('caches results and does not refetch', function () {
    fakeGitHub();

    app(GetGitHubActivityAction::class)->execute();
    app(GetGitHubActivityAction::class)->execute();

    Http::assertSentCount(2);
});

test('caches failures briefly instead of hammering the API', function () {
    fakeGitHub(500);

    expect(app(GetGitHubActivityAction::class)->execute())->toBeNull();
    $sent = count(Http::recorded());
    app(GetGitHubActivityAction::class)->execute();

    expect(count(Http::recorded()))->toBe($sent);
});

test('is disabled without a username', function () {
    config(['portfolio.github.username' => null]);
    Http::fake();

    expect(app(GetGitHubActivityAction::class)->execute())->toBeNull();
    Http::assertNothingSent();
});

test('the refresh command repopulates the cache', function () {
    fakeGitHub();

    $this->artisan('github:refresh')->assertSuccessful();

    config(['portfolio.github.username' => null]);
    $this->artisan('github:refresh')->assertFailed();
});
