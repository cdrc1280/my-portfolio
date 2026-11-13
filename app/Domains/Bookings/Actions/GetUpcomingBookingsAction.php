<?php

declare(strict_types=1);

namespace App\Domains\Bookings\Actions;

use App\Domains\Bookings\Support\CalendlyClient;
use App\Domains\Bookings\ValueObjects\BookingData;
use Illuminate\Support\Facades\Cache;

/**
 * Cached upcoming Calendly calls for the admin dashboard. Failures are cached briefly so a Calendly
 * outage never slows the dashboard down.
 */
final class GetUpcomingBookingsAction
{
    public const CACHE_KEY = 'calendly.upcoming.v1';

    private const FAILURE_TTL_MINUTES = 5;

    public function __construct(
        private readonly CalendlyClient $client,
        private readonly FetchUpcomingBookingsAction $fetch,
    ) {}

    /**
     * @return array{configured: bool, available: bool, bookings: list<array<string, string|null>>}
     */
    public function execute(): array
    {
        if (! $this->client->isConfigured()) {
            return ['configured' => false, 'available' => false, 'bookings' => []];
        }

        $cached = Cache::get(self::CACHE_KEY);

        if (! is_array($cached) && $cached !== false) {
            // Cache plain arrays, not DTOs: the cache only unserializes allow-listed classes.
            $bookings = $this->fetch->execute((int) config('portfolio.calendly.limit', 8));
            $cached = $bookings === null
                ? false
                : array_map(fn (BookingData $booking): array => $booking->toArray(), $bookings);
            $minutes = $cached === false ? self::FAILURE_TTL_MINUTES : (int) config('portfolio.calendly.cache_minutes', 10);
            Cache::put(self::CACHE_KEY, $cached, now()->addMinutes($minutes));
        }

        if ($cached === false) {
            return ['configured' => true, 'available' => false, 'bookings' => []];
        }

        /** @var list<array<string, string|null>> $cached */
        return ['configured' => true, 'available' => true, 'bookings' => $cached];
    }
}
