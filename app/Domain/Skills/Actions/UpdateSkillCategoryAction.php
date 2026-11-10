<?php

declare(strict_types=1);

namespace App\Domain\Skills\Actions;

use App\Domain\Skills\DataTransferObjects\SkillCategoryData;
use App\Domain\Skills\Models\SkillCategory;

final class UpdateSkillCategoryAction
{
    public function execute(SkillCategory $category, SkillCategoryData $data): SkillCategory
    {
        $category->update(['name' => $data->name]);

        return $category;
    }
}
