<?php

use App\Domain\Skills\Actions\CreateSkillAction;
use App\Domain\Skills\DataTransferObjects\SkillData;
use App\Domain\Skills\Models\Skill;
use App\Domain\Skills\Models\SkillCategory;

test('creates a skill at the end of the list', function () {
    $category = SkillCategory::factory()->create();
    Skill::factory()->for($category, 'category')->create(['position' => 4]);

    $skill = app(CreateSkillAction::class)->execute(SkillData::fromArray(['name' => ' Laravel ', 'skill_category_id' => $category->id]));

    expect($skill)->name->toBe('Laravel')->position->toBe(5)->skill_category_id->toBe($category->id);
});
