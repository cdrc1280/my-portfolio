<?php

use App\Domain\Skills\Actions\ReorderSkillsAction;
use App\Domain\Skills\Models\Skill;
use App\Domain\Skills\Models\SkillCategory;

test('reorders skills within a category', function () {
    $category = SkillCategory::factory()->create();
    [$a, $b] = Skill::factory()->for($category, 'category')->count(2)->create()->all();

    app(ReorderSkillsAction::class)->execute([$b->id, $a->id]);

    expect($category->skills()->pluck('id')->all())->toBe([$b->id, $a->id]);
});
