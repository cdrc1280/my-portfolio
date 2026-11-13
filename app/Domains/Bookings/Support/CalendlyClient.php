<?php

declare(strict_types=1);

namespace App\Domains\Bookings\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Thin, pre-configured HTTP client for the Calendly v2 API (personal access token).
 */
final class CalendlyClient
{
    public function __construct(
        private readonly ?string $token,
    ) {}

    public static function fromConfig(): self
    {
        $token = config('portfolio.calendly.token');

        return new self(is_string($token) && $token !== '' ? $token : null);
    }

    public function isConfigured(): bool
    {
        return $this->token !== null;
    }

    public function request(): PendingRequest
    {
        return Http::baseUrl('https://api.calendly.com')
            ->acceptJson()
            ->withToken((string) $this->token)
            ->timeout(5)
            ->retry(2, 200, throw: false);
    }
}
