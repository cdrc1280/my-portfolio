<?php

use App\Domain\Projects\Models\Project;
use App\Domain\Tags\Models\Tag;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to login', function () {
    $this->get(route('admin.tags.index'))->assertRedirect(route('login'));
});

test('non-admins are forbidden', function () {
    $tag = Tag::factory()->create();
    $this->actingAs(nonAdmin());

    $this->get(route('admin.tags.index'))->assertForbidden();
    $this->post(route('admin.tags.store'), ['name' => 'Go'])->assertForbidden();
    $this->put(route('admin.tags.update', $tag), ['name' => 'Go'])->assertForbidden();
    $this->delete(route('admin.tags.destroy', $tag))->assertForbidden();
});

test('index lists tags alphabetically with usage counts', function () {
    $tag = Tag::factory()->create(['name' => 'Vue', 'slug' => 'vue']);
    Tag::factory()->create(['name' => 'Alpine', 'slug' => 'alpine']);
    Project::factory()->create()->tags()->attach($tag);

    $this->actingAs(admin())
        ->get(route('admin.tags.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/tags/index')
            ->has('tags', 2)
            ->where('tags.0.name', 'Alpine')
            ->where('tags.1.projects_count', 1));
});

test('store creates a tag with a derived slug', function () {
    $this->actingAs(admin())
        ->post(route('admin.tags.store'), ['name' => 'Tailwind CSS'])
        ->assertRedirect(route('admin.tags.index'));

    $this->assertDatabaseHas('tags', ['name' => 'Tailwind CSS', 'slug' => 'tailwind-css']);
});

test('store reports a duplicate on the name field', function () {
    Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);

    $this->actingAs(admin())
        ->post(route('admin.tags.store'), ['name' => 'laravel'])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseCount('tags', 1);
});

test('store requires a name', function () {
    $this->actingAs(admin())->post(route('admin.tags.store'), ['name' => ''])->assertSessionHasErrors('name');
});

test('update renames a tag and may keep its slug', function () {
    $tag = Tag::factory()->create(['name' => 'Vue', 'slug' => 'vue']);

    $this->actingAs(admin())
        ->put(route('admin.tags.update', $tag), ['name' => 'Vue.js', 'slug' => 'vue'])
        ->assertRedirect(route('admin.tags.index'));

    expect($tag->fresh())->name->toBe('Vue.js')->slug->toBe('vue');
});

test('update rejects a slug taken by another tag', function () {
    Tag::factory()->create(['slug' => 'react']);
    $tag = Tag::factory()->create(['slug' => 'vue']);

    $this->actingAs(admin())
        ->put(route('admin.tags.update', $tag), ['name' => 'Vue', 'slug' => 'react'])
        ->assertSessionHasErrors('slug');
});

test('destroy deletes a tag', function () {
    $tag = Tag::factory()->create();

    $this->actingAs(admin())->delete(route('admin.tags.destroy', $tag))->assertRedirect(route('admin.tags.index'));

    $this->assertModelMissing($tag);
});
