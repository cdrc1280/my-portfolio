<?php

use App\Domains\Board\Enums\CardPriority;
use App\Domains\Board\Enums\CardStatus;
use App\Domains\Board\Models\BoardCard;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to login', function () {
    $this->get(route('admin.board.index'))->assertRedirect(route('login'));
});

test('non-admins are forbidden', function () {
    $card = BoardCard::factory()->create();
    $this->actingAs(nonAdmin());

    $this->get(route('admin.board.index'))->assertForbidden();
    $this->post(route('admin.board.cards.store'), ['title' => 'x'])->assertForbidden();
    $this->put(route('admin.board.cards.update', $card), ['title' => 'x'])->assertForbidden();
    $this->post(route('admin.board.cards.move', $card), ['status' => 'done', 'ids' => [$card->id]])->assertForbidden();
    $this->delete(route('admin.board.cards.destroy', $card))->assertForbidden();
    $this->delete(route('admin.board.clear-done'))->assertForbidden();
});

test('index lists every column in order with its cards', function () {
    BoardCard::factory()->status(CardStatus::Doing)->create(['title' => 'B', 'position' => 2]);
    BoardCard::factory()->status(CardStatus::Doing)->create(['title' => 'A', 'position' => 1]);
    BoardCard::factory()->create(['title' => 'Late', 'due_on' => today()->subDay()]);

    $this->actingAs(admin())
        ->get(route('admin.board.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/board/index')
            ->has('columns', 4)
            ->where('columns.0.status', 'ideas')
            ->where('columns.1.cards.0.title', 'Late')
            ->where('columns.1.cards.0.overdue', true)
            ->where('columns.2.cards.0.title', 'A')
            ->where('columns.2.cards.1.title', 'B')
            ->where('statuses', CardStatus::options())
            ->where('priorities', CardPriority::options()));
});

test('store adds a card with defaults', function () {
    $this->actingAs(admin())
        ->post(route('admin.board.cards.store'), ['title' => '  Write blog post  ', 'notes' => '', 'due_on' => '2026-12-01'])
        ->assertRedirect(route('admin.board.index'));

    expect(BoardCard::query()->sole())
        ->title->toBe('Write blog post')
        ->notes->toBeNull()
        ->status->toBe(CardStatus::Todo)
        ->priority->toBe(CardPriority::Normal)
        ->due_on->toDateString()->toBe('2026-12-01');
});

test('store rejects bad input', function () {
    $this->actingAs(admin())
        ->post(route('admin.board.cards.store'), ['title' => '', 'status' => 'later', 'priority' => 'urgent', 'due_on' => 'soon'])
        ->assertSessionHasErrors(['title', 'status', 'priority', 'due_on']);
});

test('update changes the fields', function () {
    $card = BoardCard::factory()->create();

    $this->actingAs(admin())
        ->put(route('admin.board.cards.update', $card), ['title' => 'Renamed', 'status' => 'doing', 'priority' => 'high'])
        ->assertRedirect(route('admin.board.index'));

    expect($card->fresh())
        ->title->toBe('Renamed')
        ->status->toBe(CardStatus::Doing)
        ->priority->toBe(CardPriority::High);
});

test('move drops a card into another column in the given order', function () {
    $card = BoardCard::factory()->create();
    $other = BoardCard::factory()->status(CardStatus::Done)->create();

    $this->actingAs(admin())
        ->post(route('admin.board.cards.move', $card), ['status' => 'done', 'ids' => [$card->id, (string) $other->id]])
        ->assertRedirect(route('admin.board.index'));

    expect($card->fresh())->status->toBe(CardStatus::Done)->position->toBe(1)
        ->and($other->fresh()->position)->toBe(2);
});

test('move rejects an unknown column', function () {
    $card = BoardCard::factory()->create();

    $this->actingAs(admin())
        ->post(route('admin.board.cards.move', $card), ['status' => 'later', 'ids' => [$card->id]])
        ->assertSessionHasErrors('status');
});

test('destroy deletes a card', function () {
    $card = BoardCard::factory()->create();

    $this->actingAs(admin())
        ->delete(route('admin.board.cards.destroy', $card))
        ->assertRedirect(route('admin.board.index'));

    expect(BoardCard::query()->count())->toBe(0);
});

test('clear done deletes only done cards', function () {
    BoardCard::factory()->status(CardStatus::Done)->count(2)->create();
    BoardCard::factory()->create();

    $this->actingAs(admin())
        ->delete(route('admin.board.clear-done'))
        ->assertRedirect(route('admin.board.index'));

    expect(BoardCard::query()->pluck('status')->all())->toBe([CardStatus::Todo]);
});
