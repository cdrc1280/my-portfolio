<?php

use App\Domain\Skills\Models\Skill;
use App\Domain\Skills\Models\SkillCategory;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to login', function () {
    $this->get(route('admin.skills.index'))->assertRedirect(route('login'));
    $this->post(route('admin.skill-categories.store'), ['name' => 'X'])->assertRedirect(route('login'));
});

test('non-admins are forbidden', function () {
    $skill = Skill::factory()->create();
    $this->actingAs(nonAdmin());

    $this->get(route('admin.skills.index'))->assertForbidden();
    $this->post(route('admin.skills.store'), ['name' => 'Go', 'skill_category_id' => $skill->skill_category_id])->assertForbidden();
    $this->delete(route('admin.skills.destroy', $skill))->assertForbidden();
    $this->post(route('admin.skill-categories.store'), ['name' => 'Ops'])->assertForbidden();
    $this->delete(route('admin.skill-categories.destroy', $skill->category))->assertForbidden();
});

test('index lists categories with ordered skills', function () {
    $category = SkillCategory::factory()->create();
    $second = Skill::factory()->for($category, 'category')->create(['position' => 2]);
    $first = Skill::factory()->for($category, 'category')->create(['position' => 1]);

    $this->actingAs(admin())
        ->get(route('admin.skills.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/skills/index')
            ->has('categories', 1)
            ->where('categories.0.skills.0.id', $first->id)
            ->where('categories.0.skills.1.id', $second->id));
});

test('store creates a skill', function () {
    $category = SkillCategory::factory()->create();

    $this->actingAs(admin())
        ->post(route('admin.skills.store'), ['name' => 'Laravel', 'skill_category_id' => $category->id])
        ->assertRedirect(route('admin.skills.index'));

    $this->assertDatabaseHas('skills', ['name' => 'Laravel', 'skill_category_id' => $category->id]);
});

test('store validates', function (array $payload, string $error) {
    $category = SkillCategory::factory()->create();
    Skill::factory()->for($category, 'category')->create(['name' => 'Taken']);

    $this->actingAs(admin())
        ->post(route('admin.skills.store'), [...['name' => 'Ok', 'skill_category_id' => $category->id], ...$payload])
        ->assertSessionHasErrors($error);
})->with([
    'missing name' => [['name' => ''], 'name'],
    'unknown category' => [['skill_category_id' => 999], 'skill_category_id'],
    'duplicate in category' => [['name' => 'Taken'], 'name'],
]);

test('update and destroy a skill', function () {
    $skill = Skill::factory()->create();
    $this->actingAs(admin());

    $this->put(route('admin.skills.update', $skill), ['name' => 'Renamed', 'skill_category_id' => $skill->skill_category_id])
        ->assertRedirect(route('admin.skills.index'));
    expect($skill->fresh()->name)->toBe('Renamed');

    $this->delete(route('admin.skills.destroy', $skill))->assertRedirect(route('admin.skills.index'));
    $this->assertModelMissing($skill);
});

test('reorder skills and categories', function () {
    $category = SkillCategory::factory()->create();
    $other = SkillCategory::factory()->create();
    [$a, $b] = Skill::factory()->for($category, 'category')->count(2)->create()->all();
    $this->actingAs(admin());

    $this->post(route('admin.skills.reorder'), ['ids' => [$b->id, $a->id]])->assertRedirect();
    $this->post(route('admin.skill-categories.reorder'), ['ids' => [$other->id, $category->id]])->assertRedirect();

    expect($b->fresh()->position)->toBe(1)->and($other->fresh()->position)->toBe(1);
});

test('categories can be created, renamed, and only deleted when empty', function () {
    $this->actingAs(admin());

    $this->post(route('admin.skill-categories.store'), ['name' => 'Backend'])->assertRedirect(route('admin.skills.index'));
    $category = SkillCategory::query()->sole();

    $this->post(route('admin.skill-categories.store'), ['name' => 'Backend'])->assertSessionHasErrors('name');

    $this->put(route('admin.skill-categories.update', $category), ['name' => 'Server'])->assertRedirect();
    expect($category->fresh()->name)->toBe('Server');

    Skill::factory()->for($category, 'category')->create();
    $this->delete(route('admin.skill-categories.destroy', $category))
        ->assertRedirect(route('admin.skills.index'))
        ->assertInertiaFlash('toast.type', 'error');
    $this->assertModelExists($category);
});
