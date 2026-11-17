<?php

use App\Domains\Board\Actions\UpdateBoardCardAction;
use App\Domains\Board\Data\BoardCardData;
use App\Domains\Board\Enums\CardPriority;
use App\Domains\Board\Enums\CardStatus;
use App\Domains\Board\Models\BoardCard;

test('a status change puts the card at the bottom of the new column', function () {
    $card = BoardCard::factory()->create();
    $last = BoardCard::factory()->status(CardStatus::Done)->create();

    app(UpdateBoardCardAction::class)->execute($card, new BoardCardData($card->title, CardStatus::Done, CardPriority::Normal));

    expect($card->fresh()->position)->toBeGreaterThan($last->position);
});
