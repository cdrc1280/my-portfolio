<?php

use App\Domain\Contact\Actions\DeleteContactMessageAction;
use App\Domain\Contact\Actions\MarkContactMessageAsReadAction;
use App\Domain\Contact\Actions\MarkContactMessageAsUnreadAction;
use App\Domain\Contact\Models\ContactMessage;

test('marks a message read without moving an existing timestamp', function () {
    $message = ContactMessage::factory()->create();

    app(MarkContactMessageAsReadAction::class)->execute($message);
    $firstRead = $message->fresh()->read_at;
    $this->travel(5)->minutes();
    app(MarkContactMessageAsReadAction::class)->execute($message->fresh());

    expect($firstRead)->not->toBeNull()
        ->and($message->fresh()->read_at->equalTo($firstRead))->toBeTrue();
});

test('marks a message unread', function () {
    $message = ContactMessage::factory()->read()->create();

    app(MarkContactMessageAsUnreadAction::class)->execute($message);

    expect($message->fresh()->isRead())->toBeFalse()
        ->and(ContactMessage::query()->unread()->count())->toBe(1);
});

test('deletes a message', function () {
    $message = ContactMessage::factory()->create();

    app(DeleteContactMessageAction::class)->execute($message);

    $this->assertModelMissing($message);
});
