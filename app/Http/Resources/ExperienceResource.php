<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Experience\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Dates are `YYYY-MM`, matching <input type="month"> in the CMS.
 *
 * @mixin Experience
 */
class ExperienceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company' => $this->company,
            'company_url' => $this->company_url,
            'role' => $this->role,
            'location' => $this->location,
            'employment_type' => $this->employment_type->value,
            'employment_type_label' => $this->employment_type->label(),
            'started_on' => $this->started_on->format('Y-m'),
            'ended_on' => $this->ended_on?->format('Y-m'),
            'is_current' => $this->isCurrent(),
            'summary' => $this->summary,
            'highlights' => $this->highlights ?? [],
            'technologies' => $this->technologies ?? [],
        ];
    }
}
