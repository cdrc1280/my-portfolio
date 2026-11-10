<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Experience\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Education
 */
class EducationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'institution' => $this->institution,
            'qualification' => $this->qualification,
            'field_of_study' => $this->field_of_study,
            'started_on' => $this->started_on->format('Y-m'),
            'ended_on' => $this->ended_on?->format('Y-m'),
            'notes' => $this->notes,
        ];
    }
}
