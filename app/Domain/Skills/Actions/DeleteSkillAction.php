<?php

declare(strict_types=1);

namespace App\Domain\Skills\Actions;

use App\Domain\Skills\Models\Skill;

final class DeleteSkillAction
{
    public function execute(Skill $skill): void
    {
        $skill->delete();
    }
}
