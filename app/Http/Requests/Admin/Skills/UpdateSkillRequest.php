<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Skills;

use App\Domain\Skills\Models\Skill;

class UpdateSkillRequest extends StoreSkillRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->skill()) ?? false;
    }

    protected function ignoredId(): ?int
    {
        return $this->skill()->id;
    }

    private function skill(): Skill
    {
        /** @var Skill */
        return $this->route('skill');
    }
}
