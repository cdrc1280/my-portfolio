<?php

use App\Domains\WorkApproach\Models\WorkApproach;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to login', function () {
    $this->get(route('admin.work-approaches.index'))->assertRedirect(route('login'));
});

test('non-admins are forbidden', function () {
    $record = WorkApproach::factory()->create();
    $this->actingAs(nonAdmin());

    $this->get(route('admin.work-approaches.index'))->assertForbidden();
    $this->post(route('admin.work-approaches.store'), ['title' => 'Tested by default', 'description' => 'Pest on every feature.', 'snippet' => '$ make ci', 'icon' => 'flask-conical'])->assertForbidden();
    $this->put(route('admin.work-approaches.update', $record), ['title' => 'Tested by default', 'description' => 'Pest on every feature.', 'snippet' => '$ make ci', 'icon' => 'flask-conical'])->assertForbidden();
    $this->delete(route('admin.work-approaches.destroy', $record))->assertForbidden();
    $this->post(route('admin.work-approaches.reorder'), ['ids' => [$record->id]])->assertForbidden();
});

test('index lists records', function () {
    WorkApproach::factory()->count(2)->create();

    $this->actingAs(admin())
        ->get(route('admin.work-approaches.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/work-approaches/index')->has('approaches', 2));
});

test('store creates a record', function () {
    $this->actingAs(admin())
        ->post(route('admin.work-approaches.store'), ['title' => 'Tested by default', 'description' => 'Pest on every feature.', 'snippet' => '$ make ci', 'icon' => 'flask-conical'])
        ->assertRedirect(route('admin.work-approaches.index'))
        ->assertInertiaFlash('toast.type', 'success');

    $this->assertDatabaseHas('work_approaches', ['snippet' => '$ make ci']);
});

test('store validates input', function () {
    $this->actingAs(admin())
        ->post(route('admin.work-approaches.store'), ['title' => '', 'description' => '', 'snippet' => str_repeat('a', 201)])
        ->assertSessionHasErrors(['title', 'description', 'snippet']);

    $this->assertDatabaseCount('work_approaches', 0);
});

test('update saves changes', function () {
    $record = WorkApproach::factory()->create();

    $this->actingAs(admin())
        ->put(route('admin.work-approaches.update', $record), ['title' => 'Tested by default', 'description' => 'Pest on every feature.', 'snippet' => '$ make ci', 'icon' => 'flask-conical'])
        ->assertRedirect(route('admin.work-approaches.index'));

    $this->assertDatabaseHas('work_approaches', ['id' => $record->id, 'snippet' => '$ make ci']);
});

test('destroy deletes the record', function () {
    $record = WorkApproach::factory()->create();

    $this->actingAs(admin())->delete(route('admin.work-approaches.destroy', $record))->assertRedirect(route('admin.work-approaches.index'));

    $this->assertModelMissing($record);
});

test('reorder persists positions', function () {
    [$a, $b] = WorkApproach::factory()->count(2)->create();

    $this->actingAs(admin())
        ->post(route('admin.work-approaches.reorder'), ['ids' => [$b->id, $a->id]])
        ->assertRedirect(route('admin.work-approaches.index'));

    expect($b->fresh()->position)->toBe(1)->and($a->fresh()->position)->toBe(2);
});
