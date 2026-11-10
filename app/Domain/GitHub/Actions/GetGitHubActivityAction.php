<?php

declare(strict_types=1);

namespace App\Domain\GitHub\Actions;

use App\Domain\GitHub\DataTransferObjects\GitHubActivityData;
use Illuminate\Support\Facades\Cache;

/**
 * Cached read of the configured user's GitHub activity. Failures are
 * cached briefly so a GitHub outage doesn't slow every page view.
 */
final class GetGitHubActivityAction
{
    private const FAILURE_TTL_MINUTES = 5;

    public function __construct(
        private readonly FetchGitHubActivityAction $fetch,
    ) {}

    public function execute(): ?GitHubActivityData
    {
        $username = self::username();

        if ($username === null) {
            return null;
        }

        $key = self::cacheKey($username);
        $cached = Cache::get($key);

        if ($cached instanceof GitHubActivityData || $cached === false) {
            return $cached ?: null;
        }

        return $this->refresh();
    }

    /**
     * Fetch now and replace the cached value.
     */
    public function refresh(): ?GitHubActivityData
    {
        $username = self::username();

        if ($username === null) {
            return null;
        }

        $activity = $this->fetch->execute($username);

        Cache::put(
            self::cacheKey($username),
            $activity ?? false,
            now()->addMinutes($activity ? (int) config('portfolio.github.cache_minutes', 60) : self::FAILURE_TTL_MINUTES),
        );

        return $activity;
    }

    public static function cacheKey(string $username): string
    {
        return "github.activity.{$username}";
    }

    private static function username(): ?string
    {
        $username = config('portfolio.github.username');

        return is_string($username) && $username !== '' ? $username : null;
    }
}
