<?php

use App\Domains\Services\Models\Service;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to login', function () {
    $this->get(route('admin.services.index'))->assertRedirect(route('login'));
});

test('non-admins are forbidden', function () {
    $record = Service::factory()->create();
    $this->actingAs(nonAdmin());

    $this->get(route('admin.services.index'))->assertForbidden();
    $this->post(route('admin.services.store'), ['title' => 'Laravel development', 'description' => 'New apps and modules.', 'icon' => 'server'])->assertForbidden();
    $this->put(route('admin.services.update', $record), ['title' => 'Laravel development', 'description' => 'New apps and modules.', 'icon' => 'server'])->assertForbidden();
    $this->delete(route('admin.services.destroy', $record))->assertForbidden();
    $this->post(route('admin.services.reorder'), ['ids' => [$record->id]])->assertForbidden();
});

test('index lists records', function () {
    Service::factory()->count(2)->create();

    $this->actingAs(admin())
        ->get(route('admin.services.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/services/index')->has('services', 2));
});

test('store creates a record', function () {
    $this->actingAs(admin())
        ->post(route('admin.services.store'), ['title' => 'Laravel development', 'description' => 'New apps and modules.', 'icon' => 'server'])
        ->assertRedirect(route('admin.services.index'))
        ->assertInertiaFlash('toast.type', 'success');

    $this->assertDatabaseHas('services', ['title' => 'Laravel development']);
});

test('store validates input', function () {
    $this->actingAs(admin())
        ->post(route('admin.services.store'), ['title' => '', 'description' => '', 'icon' => 'bad icon!'])
        ->assertSessionHasErrors(['title', 'description', 'icon']);

    $this->assertDatabaseCount('services', 0);
});

test('update saves changes', function () {
    $record = Service::factory()->create();

    $this->actingAs(admin())
        ->put(route('admin.services.update', $record), ['title' => 'Laravel development', 'description' => 'New apps and modules.', 'icon' => 'server'])
        ->assertRedirect(route('admin.services.index'));

    $this->assertDatabaseHas('services', ['id' => $record->id, 'title' => 'Laravel development']);
});

test('destroy deletes the record', function () {
    $record = Service::factory()->create();

    $this->actingAs(admin())->delete(route('admin.services.destroy', $record))->assertRedirect(route('admin.services.index'));

    $this->assertModelMissing($record);
});

test('reorder persists positions', function () {
    [$a, $b] = Service::factory()->count(2)->create();

    $this->actingAs(admin())
        ->post(route('admin.services.reorder'), ['ids' => [$b->id, $a->id]])
        ->assertRedirect(route('admin.services.index'));

    expect($b->fresh()->position)->toBe(1)->and($a->fresh()->position)->toBe(2);
});
