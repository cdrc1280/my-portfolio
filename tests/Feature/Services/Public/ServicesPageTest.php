<?php

use App\Domains\Profile\Models\Profile;
use App\Domains\Services\Models\Service;
use Inertia\Testing\AssertableInertia as Assert;

test('services page shows services, engagement types, rate and process', function () {
    Profile::factory()->create([
        'starting_rate' => '$35/hour',
        'engagement_types' => [['title' => 'Fixed project', 'description' => 'Agreed scope.']],
        'process_steps' => [['title' => 'Discovery call', 'description' => '30 minutes.']],
    ]);
    Service::factory()->count(3)->create();

    $this->get(route('services'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/services')
            ->has('services', 3)
            ->where('startingRate', '$35/hour')
            ->where('engagementTypes.0.title', 'Fixed project')
            ->where('processSteps.0.title', 'Discovery call'));
});

test('services page hides an empty rate', function () {
    Profile::factory()->create(['starting_rate' => null]);

    $this->get(route('services'))->assertInertia(fn (Assert $page) => $page->where('startingRate', null));
});
