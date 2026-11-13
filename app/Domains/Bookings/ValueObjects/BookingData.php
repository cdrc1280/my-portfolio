<?php

declare(strict_types=1);

namespace App\Domains\Bookings\ValueObjects;

use Carbon\CarbonImmutable;

/**
 * One upcoming Calendly event with its (first) invitee.
 */
final readonly class BookingData
{
    public function __construct(
        public string $name,
        public CarbonImmutable $startsAt,
        public CarbonImmutable $endsAt,
        public ?string $inviteeName = null,
        public ?string $inviteeEmail = null,
        public ?string $joinUrl = null,
        public ?string $location = null,
    ) {}

    /**
     * @param  array<string, mixed>  $event  A `scheduled_events` resource.
     * @param  array<string, mixed>|null  $invitee  An `invitees` resource.
     */
    public static function fromApi(array $event, ?array $invitee): self
    {
        /** @var array<string, mixed> $location */
        $location = is_array($event['location'] ?? null) ? $event['location'] : [];

        return new self(
            name: (string) ($event['name'] ?? 'Meeting'),
            startsAt: CarbonImmutable::parse((string) $event['start_time']),
            endsAt: CarbonImmutable::parse((string) $event['end_time']),
            inviteeName: isset($invitee['name']) ? (string) $invitee['name'] : null,
            inviteeEmail: isset($invitee['email']) ? (string) $invitee['email'] : null,
            joinUrl: isset($location['join_url']) ? (string) $location['join_url'] : null,
            location: isset($location['location']) ? (string) $location['location'] : (isset($location['type']) ? (string) $location['type'] : null),
        );
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'starts_at' => $this->startsAt->toIso8601String(),
            'ends_at' => $this->endsAt->toIso8601String(),
            'invitee_name' => $this->inviteeName,
            'invitee_email' => $this->inviteeEmail,
            'join_url' => $this->joinUrl,
            'location' => $this->location,
        ];
    }
}
