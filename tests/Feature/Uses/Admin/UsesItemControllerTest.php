<?php

use App\Domains\Uses\Models\UsesItem;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to login', function () {
    $this->get(route('admin.uses.index'))->assertRedirect(route('login'));
});

test('non-admins are forbidden', function () {
    $record = UsesItem::factory()->create();
    $this->actingAs(nonAdmin());

    $this->get(route('admin.uses.index'))->assertForbidden();
    $this->post(route('admin.uses.store'), ['category' => 'Editor', 'name' => 'VS Code', 'description' => 'Main editor.', 'url' => 'https://code.visualstudio.com'])->assertForbidden();
    $this->put(route('admin.uses.update', $record), ['category' => 'Editor', 'name' => 'VS Code', 'description' => 'Main editor.', 'url' => 'https://code.visualstudio.com'])->assertForbidden();
    $this->delete(route('admin.uses.destroy', $record))->assertForbidden();
    $this->post(route('admin.uses.reorder'), ['ids' => [$record->id]])->assertForbidden();
});

test('index lists records', function () {
    UsesItem::factory()->count(2)->create();

    $this->actingAs(admin())
        ->get(route('admin.uses.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('admin/uses/index')->has('items', 2));
});

test('store creates a record', function () {
    $this->actingAs(admin())
        ->post(route('admin.uses.store'), ['category' => 'Editor', 'name' => 'VS Code', 'description' => 'Main editor.', 'url' => 'https://code.visualstudio.com'])
        ->assertRedirect(route('admin.uses.index'))
        ->assertInertiaFlash('toast.type', 'success');

    $this->assertDatabaseHas('uses_items', ['name' => 'VS Code']);
});

test('store validates input', function () {
    $this->actingAs(admin())
        ->post(route('admin.uses.store'), ['category' => '', 'name' => '', 'url' => 'javascript:alert(1)'])
        ->assertSessionHasErrors(['category', 'name', 'url']);

    $this->assertDatabaseCount('uses_items', 0);
});

test('update saves changes', function () {
    $record = UsesItem::factory()->create();

    $this->actingAs(admin())
        ->put(route('admin.uses.update', $record), ['category' => 'Editor', 'name' => 'VS Code', 'description' => 'Main editor.', 'url' => 'https://code.visualstudio.com'])
        ->assertRedirect(route('admin.uses.index'));

    $this->assertDatabaseHas('uses_items', ['id' => $record->id, 'name' => 'VS Code']);
});

test('destroy deletes the record', function () {
    $record = UsesItem::factory()->create();

    $this->actingAs(admin())->delete(route('admin.uses.destroy', $record))->assertRedirect(route('admin.uses.index'));

    $this->assertModelMissing($record);
});

test('reorder persists positions', function () {
    [$a, $b] = UsesItem::factory()->count(2)->create();

    $this->actingAs(admin())
        ->post(route('admin.uses.reorder'), ['ids' => [$b->id, $a->id]])
        ->assertRedirect(route('admin.uses.index'));

    expect($b->fresh()->position)->toBe(1)->and($a->fresh()->position)->toBe(2);
});
