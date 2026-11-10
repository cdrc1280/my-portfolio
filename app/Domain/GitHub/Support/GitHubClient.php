<?php

declare(strict_types=1);

namespace App\Domain\GitHub\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Thin, pre-configured HTTP client for the GitHub REST/GraphQL APIs.
 */
final class GitHubClient
{
    public function __construct(
        private readonly ?string $token,
    ) {}

    public static function fromConfig(): self
    {
        $token = config('portfolio.github.token');

        return new self(is_string($token) && $token !== '' ? $token : null);
    }

    public function hasToken(): bool
    {
        return $this->token !== null;
    }

    public function request(): PendingRequest
    {
        return Http::baseUrl('https://api.github.com')
            ->acceptJson()
            ->withUserAgent('portfolio-site')
            ->when($this->token, fn (PendingRequest $request, string $token) => $request->withToken($token))
            ->timeout(5)
            ->retry(2, 200, throw: false);
    }
}
