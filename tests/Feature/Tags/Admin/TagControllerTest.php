<?php

use App\Domains\Projects\Models\Project;
use App\Domains\Tags\Enums\TagCategory;
use App\Domains\Tags\Models\Tag;
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

test('index lists tags in manual order with usage counts and categories', function () {
    $tag = Tag::factory()->create(['name' => 'Vue', 'slug' => 'vue', 'position' => 2]);
    Tag::factory()->skill(TagCategory::Frontend)->create(['name' => 'Alpine', 'slug' => 'alpine', 'position' => 1]);
    Project::factory()->create()->tags()->attach($tag);

    $this->actingAs(admin())
        ->get(route('admin.tags.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/tags/index')
            ->has('tags', 2)
            ->where('tags.0.name', 'Alpine')
            ->where('tags.0.category', 'frontend')
            ->where('tags.1.projects_count', 1)
            ->where('categories', TagCategory::options()));
});

test('store saves a skill category and icon', function () {
    $this->actingAs(admin())
        ->post(route('admin.tags.store'), ['name' => 'Pest', 'category' => 'testing', 'icon' => 'flask-conical'])
        ->assertRedirect(route('admin.tags.index'));

    expect(Tag::query()->where('slug', 'pest')->sole())
        ->category->toBe(TagCategory::Testing)
        ->icon->toBe('flask-conical');
});

test('store rejects an unknown category or a bad icon name', function () {
    $this->actingAs(admin())
        ->post(route('admin.tags.store'), ['name' => 'Go', 'category' => 'cooking', 'icon' => 'not an icon!'])
        ->assertSessionHasErrors(['category', 'icon']);
});

test('reorder persists positions', function () {
    [$a, $b] = Tag::factory()->count(2)->create();

    $this->actingAs(admin())
        ->post(route('admin.tags.reorder'), ['ids' => [$b->id, $a->id]])
        ->assertRedirect(route('admin.tags.index'));

    expect($b->fresh()->position)->toBe(1)->and($a->fresh()->position)->toBe(2);
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
