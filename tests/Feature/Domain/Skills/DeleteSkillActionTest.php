<?php

use App\Domain\Skills\Actions\DeleteSkillAction;
use App\Domain\Skills\Models\Skill;

test('deletes a skill', function () {
    $skill = Skill::factory()->create();

    app(DeleteSkillAction::class)->execute($skill);

    $this->assertModelMissing($skill);
});
