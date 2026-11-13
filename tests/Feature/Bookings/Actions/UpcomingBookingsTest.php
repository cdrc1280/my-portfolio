<?php

use App\Domains\Bookings\Actions\GetUpcomingBookingsAction;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

function fakeCalendly(): void
{
    Http::fake([
        'api.calendly.com/users/me' => Http::response(['resource' => ['uri' => 'https://api.calendly.com/users/ME']]),
        'api.calendly.com/scheduled_events/E1/invitees*' => Http::response(['collection' => [['name' => 'Maria Santos', 'email' => 'maria@example.com']]]),
        'api.calendly.com/scheduled_events*' => Http::response(['collection' => [[
            'uri' => 'https://api.calendly.com/scheduled_events/E1',
            'name' => '30 Minute Discovery Call',
            'start_time' => '2026-11-20T01:00:00.000000Z',
            'end_time' => '2026-11-20T01:30:00.000000Z',
            'location' => ['type' => 'google_conference', 'join_url' => 'https://meet.google.com/abc'],
        ]]]),
    ]);
}

test('without a token the dashboard reports calendly as not configured', function () {
    config(['portfolio.calendly.token' => null]);
    Http::fake();

    expect(app(GetUpcomingBookingsAction::class)->execute())
        ->toBe(['configured' => false, 'available' => false, 'bookings' => []]);
    Http::assertNothingSent();
});

test('fetches upcoming events with their invitee', function () {
    config(['portfolio.calendly.token' => 'secret']);
    fakeCalendly();

    $result = app(GetUpcomingBookingsAction::class)->execute();

    expect($result['configured'])->toBeTrue()
        ->and($result['available'])->toBeTrue()
        ->and($result['bookings'])->toHaveCount(1)
        ->and($result['bookings'][0])->toMatchArray([
            'name' => '30 Minute Discovery Call',
            'invitee_name' => 'Maria Santos',
            'invitee_email' => 'maria@example.com',
            'join_url' => 'https://meet.google.com/abc',
            'location' => 'google_conference',
        ]);

    Http::assertSent(fn (Request $request) => $request->hasHeader('Authorization', 'Bearer secret')
        && str_contains($request->url(), 'scheduled_events?')
        && str_contains($request->url(), 'status=active')
        && str_contains($request->url(), 'sort=start_time%3Aasc'));
});

test('results are cached', function () {
    config(['portfolio.calendly.token' => 'secret']);
    fakeCalendly();

    app(GetUpcomingBookingsAction::class)->execute();
    app(GetUpcomingBookingsAction::class)->execute();

    Http::assertSentCount(3);
});

test('an api failure is reported as unavailable and cached briefly', function () {
    config(['portfolio.calendly.token' => 'bad']);
    Http::fake(['api.calendly.com/*' => Http::response(['message' => 'Unauthenticated'], 401)]);

    expect(app(GetUpcomingBookingsAction::class)->execute())
        ->toBe(['configured' => true, 'available' => false, 'bookings' => []]);
    expect(Cache::get(GetUpcomingBookingsAction::CACHE_KEY))->toBeFalse();
});
