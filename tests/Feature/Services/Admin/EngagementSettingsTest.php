<?php

use App\Domains\Profile\Models\Profile;
use Inertia\Testing\AssertableInertia as Assert;

test('non-admins cannot change engagement settings', function () {
    $this->actingAs(nonAdmin())->put(route('admin.services.settings'), [])->assertForbidden();
});

test('the services page exposes engagement settings', function () {
    Profile::factory()->create([
        'starting_rate' => '$35/hour',
        'engagement_types' => [['title' => 'Retainer', 'description' => 'Monthly.']],
    ]);

    $this->actingAs(admin())
        ->get(route('admin.services.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('engagement.starting_rate', '$35/hour')
            ->where('engagement.engagement_types.0.title', 'Retainer')
            ->where('engagement.process_steps', []));
});

test('settings are saved, trimmed and empty rows dropped', function () {
    $this->actingAs(admin())
        ->put(route('admin.services.settings'), [
            'starting_rate' => ' $35/hour ',
            'engagement_types' => [
                ['title' => ' Part-time hourly ', 'description' => ' A few hours a week. '],
                ['title' => '', 'description' => 'ignored'],
            ],
            'process_steps' => [['title' => 'Discovery call', 'description' => '']],
        ])
        ->assertRedirect(route('admin.services.index'))
        ->assertInertiaFlash('toast.type', 'success');

    expect(Profile::query()->sole())
        ->starting_rate->toBe('$35/hour')
        ->engagement_types->toBe([['title' => 'Part-time hourly', 'description' => 'A few hours a week.']])
        ->process_steps->toBe([['title' => 'Discovery call', 'description' => '']]);
});

test('an empty rate is stored as null so it is hidden publicly', function () {
    Profile::factory()->create(['starting_rate' => '$35/hour']);

    $this->actingAs(admin())->put(route('admin.services.settings'), ['starting_rate' => ''])->assertRedirect();

    expect(Profile::query()->sole()->starting_rate)->toBeNull();
});

test('settings are validated', function () {
    $this->actingAs(admin())
        ->put(route('admin.services.settings'), [
            'starting_rate' => str_repeat('a', 101),
            'process_steps' => array_fill(0, 9, ['title' => 'Step', 'description' => '']),
        ])
        ->assertSessionHasErrors(['starting_rate', 'process_steps']);
});
