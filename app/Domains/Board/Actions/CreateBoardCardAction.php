<?php

declare(strict_types=1);

namespace App\Domains\Board\Actions;

use App\Domains\Board\Data\BoardCardData;
use App\Domains\Board\Enums\CardPriority;
use App\Domains\Board\Enums\CardStatus;
use App\Domains\Board\Models\BoardCard;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Adds a card to the bottom of its column.
 */
final class CreateBoardCardAction
{
    public function execute(BoardCardData $data): BoardCard
    {
        return DB::transaction(fn (): BoardCard => BoardCard::query()->create(self::columns($data)));
    }

    /**
     * @return array{title: string, notes: string|null, status: CardStatus, priority: CardPriority, due_on: CarbonImmutable|null}
     */
    public static function columns(BoardCardData $data): array
    {
        return [
            'title' => $data->title,
            'notes' => $data->notes,
            'status' => $data->status,
            'priority' => $data->priority,
            'due_on' => $data->dueOn,
        ];
    }
}
