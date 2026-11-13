<?php

use App\Shared\Exceptions\DomainRuleViolation;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware('web')->post('/_test/blocked', fn () => throw new DomainRuleViolation('This tag is still in use.'));
});

test('a blocked business rule sends the user back with an error toast', function () {
    $this->from('/somewhere')
        ->post('/_test/blocked')
        ->assertRedirect('/somewhere')
        ->assertInertiaFlash('toast.type', 'error')
        ->assertInertiaFlash('toast.message', 'This tag is still in use.');
});

test('json callers get a 422 with the message', function () {
    $this->postJson('/_test/blocked')
        ->assertStatus(422)
        ->assertJson(['message' => 'This tag is still in use.']);
});
