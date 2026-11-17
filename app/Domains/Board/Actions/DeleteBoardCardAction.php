<?php

declare(strict_types=1);

namespace App\Domains\Board\Actions;

use App\Domains\Board\Models\BoardCard;

final class DeleteBoardCardAction
{
    public function execute(BoardCard $card): void
    {
        $card->delete();
    }
}
