<?php

use App\Domains\Contact\Events\ContactMessageSubmitted;
use App\Domains\Contact\Models\ContactMessage;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(fn () => Event::fake([ContactMessageSubmitted::class]));

function contactPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'project_type' => 'new_build',
        'budget' => '1k_3k',
        'budget_currency' => 'PHP',
        'timeline' => 'flexible',
        'message' => 'I would love to work with you on something.',
        'website' => '',
    ], $overrides);
}

test('the contact page renders with the brief options', function () {
    $this->get(route('contact.show'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/contact')
            ->has('projectTypes', 6)
            ->has('currencies', 7)
            ->has('budgets.USD', 6)
            ->has('budgets.PHP', 6)
            ->has('timelines', 5)
            ->where('otherValue', 'other')
            ->where('budgets.USD.0', ['value' => 'under_1k', 'label' => 'Under $1,000'])
            ->where('budgets.PHP.1', ['value' => '1k_3k', 'label' => '₱50,000 – ₱150,000']));
});

test('a valid submission is stored and acknowledged', function () {
    $this->post(route('contact.store'), contactPayload())
        ->assertRedirect(route('contact.show'))
        ->assertInertiaFlash('toast.type', 'success');

    expect(ContactMessage::query()->sole())
        ->email->toBe('ada@example.com')
        ->budget->value->toBe('1k_3k')
        ->budget_currency->value->toBe('PHP')
        ->ip_hash->not->toBeNull();
    Event::assertDispatched(ContactMessageSubmitted::class);
});

test('validation errors are returned', function (array $payload, string $error) {
    $this->post(route('contact.store'), contactPayload($payload))->assertSessionHasErrors($error);

    $this->assertDatabaseCount('contact_messages', 0);
})->with([
    'missing name' => [['name' => ''], 'name'],
    'bad email' => [['email' => 'not-an-email'], 'email'],
    'missing project type' => [['project_type' => ''], 'project_type'],
    'unknown budget' => [['budget' => 'millions'], 'budget'],
    'missing currency' => [['budget_currency' => ''], 'budget_currency'],
    'unknown currency' => [['budget_currency' => 'XYZ'], 'budget_currency'],
    'unknown timeline' => [['timeline' => 'yesterday'], 'timeline'],
    'message too short' => [['message' => 'hi there'], 'message'],
    'message too long' => [['message' => str_repeat('a', 5001)], 'message'],
]);

test('honeypot submissions look successful but are discarded', function () {
    $this->post(route('contact.store'), contactPayload(['website' => 'https://spam.test']))
        ->assertRedirect(route('contact.show'))
        ->assertInertiaFlash('toast.type', 'success');

    $this->assertDatabaseCount('contact_messages', 0);
    Event::assertNotDispatched(ContactMessageSubmitted::class);
});

test('submissions are limited to three per hour per ip', function () {
    foreach (range(1, 3) as $_) {
        $this->post(route('contact.store'), contactPayload())->assertRedirect();
    }

    $this->post(route('contact.store'), contactPayload())->assertTooManyRequests();
    $this->assertDatabaseCount('contact_messages', 3);

    $this->travel(61)->minutes();
    $this->post(route('contact.store'), contactPayload())->assertRedirect();
    $this->assertDatabaseCount('contact_messages', 4);
});

test('picking "other" stores the sender\'s own words', function () {
    $this->post(route('contact.store'), contactPayload([
        'project_type' => 'other',
        'project_type_other' => '  Mobile app  ',
        'budget' => 'other',
        'budget_other' => '₱20,000 a month',
        'timeline' => 'other',
        'timeline_other' => 'Before March',
    ]))->assertRedirect(route('contact.show'));

    $message = ContactMessage::query()->sole();
    expect($message->project_type_other)->toBe('Mobile app')
        ->and($message->projectTypeLabel())->toBe('Mobile app')
        ->and($message->budgetLabel())->toBe('₱20,000 a month')
        ->and($message->timelineLabel())->toBe('Before March');
});

test('"other" needs the sender\'s own words', function (string $field) {
    $this->post(route('contact.store'), contactPayload([$field => 'other', "{$field}_other" => '  ']))
        ->assertSessionHasErrors("{$field}_other");

    $this->assertDatabaseCount('contact_messages', 0);
})->with(['project_type', 'budget', 'timeline']);

test('own words are dropped when a listed option is picked', function () {
    $this->post(route('contact.store'), contactPayload(['project_type_other' => 'Leftover text']))
        ->assertRedirect(route('contact.show'));

    expect(ContactMessage::query()->sole())
        ->project_type_other->toBeNull()
        ->projectTypeLabel()->toBe('New web application');
});
