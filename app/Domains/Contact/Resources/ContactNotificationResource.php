<?php

declare(strict_types=1);

namespace App\Domains\Contact\Resources;

use App\Domains\Contact\Models\ContactMessage;
use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * An unread brief in the admin notification bell: who sent it, what it's about and a short excerpt.
 */
#[MapName(SnakeCaseMapper::class)]
final class ContactNotificationResource extends Resource
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $projectType,
        public string $excerpt,
        public ?string $createdAt,
        /** e.g. "2 hours ago". */
        public ?string $receivedAgo,
    ) {}

    public static function fromModel(ContactMessage $message): self
    {
        return new self(
            id: $message->id,
            name: $message->name,
            projectType: $message->projectTypeLabel(),
            excerpt: Str::limit(Str::squish($message->message), 90),
            createdAt: $message->created_at?->toIso8601String(),
            receivedAgo: $message->created_at?->diffForHumans(),
        );
    }
}
