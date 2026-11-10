<?php

declare(strict_types=1);

namespace App\Domain\Skills\Actions;

use App\Domain\Skills\DataTransferObjects\SkillData;
use App\Domain\Skills\Models\Skill;

final class CreateSkillAction
{
    public function execute(SkillData $data): Skill
    {
        return Skill::query()->create($data->toArray());
    }
}
