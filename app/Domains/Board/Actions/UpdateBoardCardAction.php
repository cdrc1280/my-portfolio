<?php

declare(strict_types=1);

namespace App\Domains\Board\Actions;

use App\Domains\Board\Data\BoardCardData;
use App\Domains\Board\Models\BoardCard;
use Illuminate\Support\Facades\DB;

/**
 * Saves a card's fields. A status change moves it to the bottom of the new column.
 */
final class UpdateBoardCardAction
{
    public function execute(BoardCard $card, BoardCardData $data): BoardCard
    {
        return DB::transaction(function () use ($card, $data): BoardCard {
            $columns = CreateBoardCardAction::columns($data);

            if ($data->status !== $card->status) {
                $columns['position'] = (int) BoardCard::query()->max('position') + 1;
            }

            $card->update($columns);

            return $card;
        });
    }
}
