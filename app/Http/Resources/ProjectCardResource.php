<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Compact project shape for lists and cards on the public site.
 *
 * @mixin Project
 */
class ProjectCardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'role' => $this->role,
            'period' => $this->period,
            'cover_url' => MediaUrl::for($this->cover_image),
            'is_featured' => $this->is_featured,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
