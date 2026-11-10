<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Skills\Models\Skill;
use App\Domain\Skills\Models\SkillCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A category with its ordered skills. Used by both the site and the CMS.
 *
 * @mixin SkillCategory
 */
class SkillCategoryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'skills' => $this->whenLoaded('skills', fn () => $this->skills->map(fn (Skill $skill): array => [
                'id' => $skill->id,
                'name' => $skill->name,
            ])->values()->all()),
        ];
    }
}
