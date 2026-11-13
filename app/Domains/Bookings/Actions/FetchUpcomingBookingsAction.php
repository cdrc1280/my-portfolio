<?php

declare(strict_types=1);

namespace App\Domains\Bookings\Actions;

use App\Domains\Bookings\Support\CalendlyClient;
use App\Domains\Bookings\ValueObjects\BookingData;
use Illuminate\Http\Client\Response;

/**
 * Calls the Calendly API: the token's user, then their next active scheduled events and each
 * event's invitee. Returns null on any API failure.
 */
final class FetchUpcomingBookingsAction
{
    public function __construct(
        private readonly CalendlyClient $client,
    ) {}

    /**
     * @return list<BookingData>|null
     */
    public function execute(int $limit): ?array
    {
        $me = $this->client->request()->get('/users/me');
        $userUri = $me->json('resource.uri');

        if (! $me->successful() || ! is_string($userUri)) {
            return null;
        }

        $events = $this->client->request()->get('/scheduled_events', [
            'user' => $userUri,
            'status' => 'active',
            'min_start_time' => now()->toIso8601ZuluString(),
            'sort' => 'start_time:asc',
            'count' => $limit,
        ]);

        if (! $events->successful()) {
            return null;
        }

        $bookings = [];

        /** @var array<string, mixed> $event */
        foreach ((array) $events->json('collection', []) as $event) {
            $bookings[] = BookingData::fromApi($event, $this->firstInvitee($event));
        }

        return $bookings;
    }

    /**
     * @param  array<string, mixed>  $event
     * @return array<string, mixed>|null
     */
    private function firstInvitee(array $event): ?array
    {
        $uri = $event['uri'] ?? null;

        if (! is_string($uri)) {
            return null;
        }

        /** @var Response $response */
        $response = $this->client->request()->get($uri.'/invitees', ['count' => 1]);
        $invitee = $response->json('collection.0');

        return $response->successful() && is_array($invitee) ? $invitee : null;
    }
}
