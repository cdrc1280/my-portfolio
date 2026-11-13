<?php

use App\Domains\Profile\Models\Profile;
use App\Domains\Services\Models\Service;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(fn () => Profile::factory()->create());

test('analytics is off until configured', function () {
    $this->get(route('home'))->assertOk()->assertDontSee('plausible.io', false);
});

test('plausible loads on public pages when configured', function () {
    config(['portfolio.analytics' => ['provider' => 'plausible', 'site_id' => 'example.com', 'script_url' => null]]);

    $this->get(route('home'))->assertSee('data-domain="example.com"', false);
});

test('admin pages are never tracked', function () {
    config(['portfolio.analytics' => ['provider' => 'plausible', 'site_id' => 'example.com', 'script_url' => null]]);

    $this->actingAs(admin())->get(route('admin.dashboard'))->assertOk()->assertDontSee('data-domain="example.com"', false);
});

test('umami needs its script url', function () {
    config(['portfolio.analytics' => ['provider' => 'umami', 'site_id' => 'abc-123', 'script_url' => 'https://stats.example.com/script.js']]);

    $this->get(route('home'))->assertSee('data-website-id="abc-123"', false);
});

test('navigation knows which optional pages have content', function () {
    $this->get(route('home'))->assertInertia(fn (Assert $page) => $page
        ->where('content.services', false)
        ->where('content.uses', false));

    Service::factory()->create();

    $this->get(route('home'))->assertInertia(fn (Assert $page) => $page->where('content.services', true));
});
