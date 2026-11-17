<?php

declare(strict_types=1);

namespace App\Domains\Board\Queries;

use App\Domains\Board\Enums\CardStatus;
use App\Domains\Board\Models\BoardCard;
use App\Domains\Board\Resources\BoardCardResource;

/**
 * Read side of the Board module.
 */
final class BoardQueries
{
    /**
     * Every column in board order with its cards in manual order.
     *
     * @return list<array{status: string, label: string, cards: list<BoardCardResource>}>
     */
    public function columns(): array
    {
        $cards = BoardCard::query()->ordered()->get()->groupBy(fn (BoardCard $card): string => $card->status->value);

        return array_map(fn (CardStatus $status): array => [
            'status' => $status->value,
            'label' => $status->label(),
            'cards' => array_values(($cards->get($status->value) ?? collect())
                ->map(fn (BoardCard $card): BoardCardResource => BoardCardResource::fromModel($card))
                ->all()),
        ], CardStatus::cases());
    }
}
