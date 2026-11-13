<?php

use App\Domains\Contact\Events\ContactMessageSubmitted;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Route::middleware('web')->post('/_test/expired', fn () => throw new TokenMismatchException);
    Route::middleware('web')->get('/_test/teapot', fn () => abort(418));
});

/**
 * @return array<string, string>
 */
function inertiaHeaders(): array
{
    return ['X-Inertia' => 'true', 'X-Requested-With' => 'XMLHttpRequest'];
}

test('a rate-limited contact form returns to the form with a toast, not a bare error page', function () {
    Event::fake([ContactMessageSubmitted::class]);
    config(['portfolio.contact.per_hour' => 1]);

    $payload = [
        'name' => 'Ada', 'email' => 'ada@example.com', 'project_type' => 'new_build', 'budget' => 'not_sure',
        'budget_currency' => 'PHP', 'timeline' => 'flexible', 'message' => 'I would love to talk about a project.',
    ];

    $this->from(route('contact.show'))->withHeaders(inertiaHeaders())->post(route('contact.store'), $payload)
        ->assertRedirect(route('contact.show'));

    $this->from(route('contact.show'))->withHeaders(inertiaHeaders())->post(route('contact.store'), $payload)
        ->assertStatus(303)
        ->assertRedirect(route('contact.show'))
        ->assertInertiaFlash('toast.type', 'error')
        ->assertInertiaFlash('toast.message', 'Too many attempts. Please try again in an hour.');
});

test('an expired session on a form submit becomes a toast', function () {
    $this->from('/somewhere')->withHeaders(inertiaHeaders())->post('/_test/expired')
        ->assertStatus(303)
        ->assertRedirect('/somewhere')
        ->assertInertiaFlash('toast.message', 'Your session expired. Please try again.');
});

test('any error on a page load renders the themed error page', function () {
    $this->get('/_test/teapot')
        ->assertStatus(418)
        ->assertInertia(fn (Assert $page) => $page->component('error')->where('status', 418));

    $this->get('/definitely-not-a-page')
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page->component('error')->where('status', 404));
});

test('json callers keep the plain error response', function () {
    $this->getJson('/_test/teapot')->assertStatus(418)->assertJsonStructure(['message']);
});
