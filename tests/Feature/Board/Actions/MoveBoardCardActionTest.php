<?php

use App\Domains\Board\Actions\MoveBoardCardAction;
use App\Domains\Board\Data\MoveCardData;
use App\Domains\Board\Enums\CardStatus;
use App\Domains\Board\Models\BoardCard;
use App\Shared\Exceptions\DomainRuleViolation;

test('a card moves to another column at the given place', function () {
    [$a, $b] = BoardCard::factory()->status(CardStatus::Doing)->count(2)->create();
    $card = BoardCard::factory()->create();

    app(MoveBoardCardAction::class)->execute($card, new MoveCardData(CardStatus::Doing, [$a->id, $card->id, $b->id]));

    expect($card->fresh())->status->toBe(CardStatus::Doing)->position->toBe(2)
        ->and($b->fresh()->position)->toBe(3);
});

test('the moved card must be in the new order', function () {
    $card = BoardCard::factory()->create();
    $other = BoardCard::factory()->create();

    app(MoveBoardCardAction::class)->execute($card, new MoveCardData(CardStatus::Todo, [$other->id]));
})->throws(DomainRuleViolation::class);

test('ids from another column are rejected and nothing changes', function () {
    $card = BoardCard::factory()->create();
    $done = BoardCard::factory()->status(CardStatus::Done)->create();

    expect(fn () => app(MoveBoardCardAction::class)->execute($card, new MoveCardData(CardStatus::Doing, [$card->id, $done->id])))
        ->toThrow(DomainRuleViolation::class);

    expect($card->fresh()->status)->toBe(CardStatus::Todo);
});
