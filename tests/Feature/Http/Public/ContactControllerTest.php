<?php

use App\Domain\Contact\Events\ContactMessageSubmitted;
use App\Domain\Contact\Models\ContactMessage;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(fn () => Event::fake([ContactMessageSubmitted::class]));

function contactPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'subject' => 'Project',
        'message' => 'I would love to work with you on something.',
        'website' => '',
    ], $overrides);
}

test('the contact page renders', function () {
    $this->get(route('contact.show'))->assertOk()->assertInertia(fn (Assert $page) => $page->component('public/contact'));
});

test('a valid submission is stored and acknowledged', function () {
    $this->post(route('contact.store'), contactPayload())
        ->assertRedirect(route('contact.show'))
        ->assertInertiaFlash('toast.type', 'success');

    expect(ContactMessage::query()->sole())->email->toBe('ada@example.com');
    Event::assertDispatched(ContactMessageSubmitted::class);
});

test('validation errors are returned', function (array $payload, string $error) {
    $this->post(route('contact.store'), contactPayload($payload))->assertSessionHasErrors($error);

    $this->assertDatabaseCount('contact_messages', 0);
})->with([
    'missing name' => [['name' => ''], 'name'],
    'bad email' => [['email' => 'not-an-email'], 'email'],
    'message too short' => [['message' => 'hi'], 'message'],
    'message too long' => [['message' => str_repeat('a', 5001)], 'message'],
]);

test('honeypot submissions look successful but are discarded', function () {
    $this->post(route('contact.store'), contactPayload(['website' => 'https://spam.test']))
        ->assertRedirect(route('contact.show'))
        ->assertInertiaFlash('toast.type', 'success');

    $this->assertDatabaseCount('contact_messages', 0);
    Event::assertNotDispatched(ContactMessageSubmitted::class);
});

test('submissions are rate limited per minute', function () {
    foreach (range(1, 3) as $_) {
        $this->post(route('contact.store'), contactPayload())->assertRedirect();
    }

    $this->post(route('contact.store'), contactPayload())->assertTooManyRequests();
    $this->assertDatabaseCount('contact_messages', 3);
});
