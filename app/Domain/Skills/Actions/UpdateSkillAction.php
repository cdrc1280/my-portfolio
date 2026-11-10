<?php

declare(strict_types=1);

namespace App\Domain\Skills\Actions;

use App\Domain\Skills\DataTransferObjects\SkillData;
use App\Domain\Skills\Models\Skill;

final class UpdateSkillAction
{
    /**
     * Moving a skill to another category appends it to that category's end.
     */
    public function execute(Skill $skill, SkillData $data): Skill
    {
        $movedCategory = $skill->skill_category_id !== $data->categoryId;

        $skill->fill($data->toArray());

        if ($movedCategory) {
            $skill->position = (int) Skill::query()->where('skill_category_id', $data->categoryId)->max('position') + 1;
        }

        $skill->save();

        return $skill;
    }
}
