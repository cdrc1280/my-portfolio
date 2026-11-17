<?php

declare(strict_types=1);

namespace App\Domains\Board\Resources;

use App\Domains\Board\Enums\CardStatus;
use App\Domains\Board\Models\BoardCard;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A card on the admin board. `overdue` is true for an open card whose due date has passed.
 */
#[MapName(SnakeCaseMapper::class)]
final class BoardCardResource extends Resource
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $notes,
        public string $status,
        public string $priority,
        public string $priorityLabel,
        public ?string $dueOn,
        public bool $overdue,
        public bool $dueToday,
        public int $position,
    ) {}

    public static function fromModel(BoardCard $card): self
    {
        $open = $card->status !== CardStatus::Done;

        return new self(
            id: $card->id,
            title: $card->title,
            notes: $card->notes,
            status: $card->status->value,
            priority: $card->priority->value,
            priorityLabel: $card->priority->label(),
            dueOn: $card->due_on?->toDateString(),
            overdue: $open && $card->due_on !== null && $card->due_on->isBefore(today()),
            dueToday: $open && $card->due_on !== null && $card->due_on->isToday(),
            position: $card->position,
        );
    }
}
