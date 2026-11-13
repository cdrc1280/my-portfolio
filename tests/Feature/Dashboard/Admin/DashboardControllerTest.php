<?php

use App\Domains\Profile\Models\Profile;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});

test('non-admin users are forbidden', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('admins can visit the dashboard', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/dashboard')
            ->where('stats.unread_messages', 0)
            ->has('stats.published_projects')
            ->has('recentMessages', 0));
});

test('the dashboard includes leads, content health and availability, with external data deferred', function () {
    config(['portfolio.github.username' => null, 'portfolio.calendly.token' => null]);

    $this->actingAs(admin())
        ->get(route('admin.dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('leads.budgets')
            ->has('health', 7)
            ->where('availability.status', 'available')
            ->has('availability.options', 3)
            ->missing('bookings')
            ->missing('github')
            ->loadDeferredProps('external', fn (Assert $reload) => $reload
                ->where('bookings.configured', false)
                ->where('github', null)));
});

test('admins can switch availability from the dashboard', function () {
    $this->actingAs(admin())
        ->from(route('admin.dashboard'))
        ->patch(route('admin.availability.update'), ['availability_status' => 'limited', 'availability' => ' Two slots left '])
        ->assertRedirect(route('admin.dashboard'))
        ->assertInertiaFlash('toast.type', 'success');

    expect(Profile::query()->sole())
        ->availability_status->value->toBe('limited')
        ->availability->toBe('Two slots left');
});

test('availability is validated and admin-only', function () {
    $this->actingAs(admin())
        ->patch(route('admin.availability.update'), ['availability_status' => 'on-vacation'])
        ->assertSessionHasErrors('availability_status');

    $this->actingAs(nonAdmin())
        ->patch(route('admin.availability.update'), ['availability_status' => 'limited'])
        ->assertForbidden();
});
