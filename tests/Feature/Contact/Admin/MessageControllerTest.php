<?php

use App\Domains\Contact\Models\ContactMessage;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected and non-admins forbidden', function () {
    $message = ContactMessage::factory()->create();

    $this->get(route('admin.messages.index'))->assertRedirect(route('login'));

    $this->actingAs(nonAdmin());
    $this->get(route('admin.messages.index'))->assertForbidden();
    $this->get(route('admin.messages.show', $message))->assertForbidden();
    $this->delete(route('admin.messages.destroy', $message))->assertForbidden();

    expect($message->fresh()->isRead())->toBeFalse();
});

test('index lists newest first and filters unread', function () {
    $old = ContactMessage::factory()->read()->create(['created_at' => now()->subDay()]);
    $new = ContactMessage::factory()->create();
    $this->actingAs(admin());

    $this->get(route('admin.messages.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/messages/index')
            ->has('messages.data', 2)
            ->where('messages.data.0.id', $new->id)
            ->where('messages.meta.total', 2)
            ->where('filter', 'all')
            ->where('unreadCount', 1));

    $this->get(route('admin.messages.index', ['filter' => 'unread']))
        ->assertInertia(fn (Assert $page) => $page->has('messages.data', 1)->where('filter', 'unread'));

    expect($old->exists)->toBeTrue();
});

test('opening a message marks it read', function () {
    $message = ContactMessage::factory()->create();

    $this->actingAs(admin())
        ->get(route('admin.messages.show', $message))
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/messages/show')
            ->where('message.id', $message->id)
            ->where('message.is_read', true));

    expect($message->fresh()->isRead())->toBeTrue();
});

test('a message can be marked unread and deleted', function () {
    $message = ContactMessage::factory()->read()->create();
    $this->actingAs(admin());

    $this->patch(route('admin.messages.unread', $message))->assertRedirect(route('admin.messages.index'));
    expect($message->fresh()->isRead())->toBeFalse();

    $this->delete(route('admin.messages.destroy', $message))->assertRedirect(route('admin.messages.index'));
    $this->assertModelMissing($message);
});
