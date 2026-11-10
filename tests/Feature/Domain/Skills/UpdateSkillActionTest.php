<?php

use App\Domain\Skills\Actions\UpdateSkillAction;
use App\Domain\Skills\DataTransferObjects\SkillData;
use App\Domain\Skills\Models\Skill;
use App\Domain\Skills\Models\SkillCategory;

test('renames a skill in place', function () {
    $skill = Skill::factory()->create(['position' => 3]);

    app(UpdateSkillAction::class)->execute($skill, new SkillData('Renamed', $skill->skill_category_id));

    expect($skill->fresh())->name->toBe('Renamed')->position->toBe(3);
});

test('moving to another category appends it there', function () {
    $target = SkillCategory::factory()->create();
    Skill::factory()->for($target, 'category')->create(['position' => 9]);
    $skill = Skill::factory()->create(['position' => 1]);

    app(UpdateSkillAction::class)->execute($skill, new SkillData($skill->name, $target->id));

    expect($skill->fresh())->skill_category_id->toBe($target->id)->position->toBe(10);
});
