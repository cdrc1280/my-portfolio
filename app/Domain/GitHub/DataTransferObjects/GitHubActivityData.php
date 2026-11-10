<?php

declare(strict_types=1);

namespace App\Domain\GitHub\DataTransferObjects;

/**
 * Public GitHub activity summary. Only public repositories are included,
 * so nothing confidential (private client work) can leak onto the site.
 */
final readonly class GitHubActivityData
{
    /**
     * @param  list<array{name: string, share: int}>  $languages
     * @param  list<array{name: string, url: string, description: string|null, language: string|null, stars: int, pushed_at: string}>  $recentRepos
     */
    public function __construct(
        public string $username,
        public string $profileUrl,
        public int $publicRepos,
        public int $followers,
        public int $totalStars,
        public ?int $contributionsLastYear,
        public array $languages,
        public array $recentRepos,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'profile_url' => $this->profileUrl,
            'public_repos' => $this->publicRepos,
            'followers' => $this->followers,
            'total_stars' => $this->totalStars,
            'contributions_last_year' => $this->contributionsLastYear,
            'languages' => $this->languages,
            'recent_repos' => $this->recentRepos,
        ];
    }
}
