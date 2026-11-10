<?php

declare(strict_types=1);

namespace App\Domain\Skills\Actions;

use App\Domain\Skills\DataTransferObjects\SkillCategoryData;
use App\Domain\Skills\Models\SkillCategory;

final class CreateSkillCategoryAction
{
    public function execute(SkillCategoryData $data): SkillCategory
    {
        return SkillCategory::query()->create(['name' => $data->name]);
    }
}
