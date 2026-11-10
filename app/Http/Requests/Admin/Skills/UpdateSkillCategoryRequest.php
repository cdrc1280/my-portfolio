<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Skills;

use App\Domain\Skills\Models\SkillCategory;

class UpdateSkillCategoryRequest extends StoreSkillCategoryRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->category()) ?? false;
    }

    protected function ignoredId(): ?int
    {
        return $this->category()->id;
    }

    private function category(): SkillCategory
    {
        /** @var SkillCategory */
        return $this->route('skill_category');
    }
}
