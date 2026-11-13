<?php

use App\Domains\Experience\Models\Certificate;
use App\Domains\Experience\Models\Experience;
use App\Domains\Profile\Models\Profile;
use Inertia\Testing\AssertableInertia as Assert;

test('about shows the rendered bio, full history and currently learning', function () {
    Profile::factory()->create(['bio' => '## Hello', 'phone' => '0938-000', 'currently_learning' => 'Rust']);
    Experience::factory()->count(5)->create();
    Certificate::factory()->count(2)->create();

    $this->get(route('about'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/about')
            ->where('profile.bio_html', fn (string $html) => str_contains($html, '<h2>Hello</h2>'))
            ->where('profile.currently_learning', 'Rust')
            ->missing('profile.phone')
            ->has('experiences', 5)
            ->has('certificates', 2)
            ->has('education'));
});

test('confidential experience never reveals the company', function () {
    Experience::factory()->create(['company' => 'Acme Secret Corp', 'company_url' => 'https://acme.test', 'is_confidential' => true]);

    $this->get(route('about'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('experiences.0.company', 'Confidential client')
            ->where('experiences.0.company_url', null))
        ->assertDontSee('Acme Secret Corp');
});
