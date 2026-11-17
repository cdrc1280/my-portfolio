<?php

declare(strict_types=1);

namespace App\Domains\Board\Actions;

use App\Domains\Board\Enums\CardStatus;
use App\Domains\Board\Models\BoardCard;
use Illuminate\Support\Facades\DB;

/**
 * Deletes every card in the Done column. Each is deleted as a model so the audit log records it.
 */
final class ClearDoneCardsAction
{
    public function execute(): int
    {
        return DB::transaction(function (): int {
            $cards = BoardCard::query()->where('status', CardStatus::Done)->get();
            $cards->each(fn (BoardCard $card) => $card->delete());

            return $cards->count();
        });
    }
}
