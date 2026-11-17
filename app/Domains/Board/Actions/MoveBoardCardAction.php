<?php

declare(strict_types=1);

namespace App\Domains\Board\Actions;

use App\Domains\Board\Data\MoveCardData;
use App\Domains\Board\Models\BoardCard;
use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\DB;

/**
 * Drops a card into a column (its own or another) and saves that column's new order.
 */
final class MoveBoardCardAction
{
    public function execute(BoardCard $card, MoveCardData $data): void
    {
        if (! in_array($card->id, $data->ids, true)) {
            throw new DomainRuleViolation('The moved card is missing from the new order. Reload the board and try again.');
        }

        DB::transaction(function () use ($card, $data): void {
            $card->update(['status' => $data->status]);

            $inColumn = BoardCard::query()->whereKey($data->ids)->where('status', $data->status)->count();

            if ($inColumn !== count($data->ids)) {
                throw new DomainRuleViolation('The board changed since it was loaded. Reload it and try again.');
            }

            foreach ($data->ids as $index => $id) {
                BoardCard::query()->whereKey($id)->update(['position' => $index + 1]);
            }
        });
    }
}
