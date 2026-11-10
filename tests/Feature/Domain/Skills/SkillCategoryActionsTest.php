<?php

use App\Domain\Skills\Actions\CreateSkillCategoryAction;
use App\Domain\Skills\Actions\DeleteSkillCategoryAction;
use App\Domain\Skills\Actions\ReorderSkillCategoriesAction;
use App\Domain\Skills\Actions\UpdateSkillCategoryAction;
use App\Domain\Skills\DataTransferObjects\SkillCategoryData;
use App\Domain\Skills\Exceptions\SkillCategoryNotEmpty;
use App\Domain\Skills\Models\Skill;
use App\Domain\Skills\Models\SkillCategory;

test('creates a category', function () {
    $category = app(CreateSkillCategoryAction::class)->execute(SkillCategoryData::fromArray(['name' => ' Backend ']));

    expect($category->name)->toBe('Backend');
});

test('renames a category', function () {
    $category = SkillCategory::factory()->create();

    app(UpdateSkillCategoryAction::class)->execute($category, new SkillCategoryData('Frontend'));

    expect($category->fresh()->name)->toBe('Frontend');
});

test('deletes an empty category', function () {
    $category = SkillCategory::factory()->create();

    app(DeleteSkillCategoryAction::class)->execute($category);

    $this->assertModelMissing($category);
});

test('refuses to delete a category that still has skills', function () {
    $skill = Skill::factory()->create();

    expect(fn () => app(DeleteSkillCategoryAction::class)->execute($skill->category))
        ->toThrow(SkillCategoryNotEmpty::class);

    $this->assertModelExists($skill->category);
});

test('reorders categories', function () {
    [$a, $b] = SkillCategory::factory()->count(2)->create()->all();

    app(ReorderSkillCategoriesAction::class)->execute([$b->id, $a->id]);

    expect(SkillCategory::query()->ordered()->pluck('id')->all())->toBe([$b->id, $a->id]);
});
