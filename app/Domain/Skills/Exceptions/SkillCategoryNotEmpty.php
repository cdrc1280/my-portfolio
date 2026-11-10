<?php

declare(strict_types=1);

namespace App\Domain\Skills\Exceptions;

use App\Domain\Skills\Models\SkillCategory;
use DomainException;

final class SkillCategoryNotEmpty extends DomainException
{
    public static function for(SkillCategory $category): self
    {
        return new self("Move or delete the skills in \"{$category->name}\" before deleting it.");
    }
}
