<?php

declare(strict_types=1);

namespace App\Domain\Skills\Actions;

use App\Domain\Skills\Exceptions\SkillCategoryNotEmpty;
use App\Domain\Skills\Models\SkillCategory;

final class DeleteSkillCategoryAction
{
    /**
     * @throws SkillCategoryNotEmpty when the category still holds skills
     */
    public function execute(SkillCategory $category): void
    {
        if ($category->skills()->exists()) {
            throw SkillCategoryNotEmpty::for($category);
        }

        $category->delete();
    }
}
