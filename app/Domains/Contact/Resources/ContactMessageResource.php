<?php

declare(strict_types=1);

namespace App\Domains\Contact\Resources;

use App\Domains\Contact\Models\ContactMessage;
use Illuminate\Support\Str;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A brief in the admin inbox. Enum fields arrive as labels (or the sender's own words for "other"); the budget is in
 * the sender's currency.
 */
#[MapName(SnakeCaseMapper::class)]
final class ContactMessageResource extends Resource
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?string $projectType,
        public ?string $budget,
        public ?string $timeline,
        public string $excerpt,
        public string $message,
        public bool $isRead,
        public ?string $readAt,
        public ?string $createdAt,
    ) {}

    public static function fromModel(ContactMessage $message): self
    {
        return new self(
            id: $message->id,
            name: $message->name,
            email: $message->email,
            projectType: $message->projectTypeLabel(),
            budget: $message->budgetLabel(),
            timeline: $message->timelineLabel(),
            excerpt: Str::limit(Str::squish($message->message), 120),
            message: $message->message,
            isRead: $message->isRead(),
            readAt: $message->read_at?->toIso8601String(),
            createdAt: $message->created_at?->toIso8601String(),
        );
    }
}
