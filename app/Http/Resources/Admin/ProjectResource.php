<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Domain\Projects\Models\Project;
use App\Domain\Projects\Models\ProjectImage;
use App\Domain\Shared\Support\MediaUrl;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Editable project shape for the CMS (raw markdown, status, ids).
 *
 * @mixin Project
 */
class ProjectResource extends JsonResource
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
            'body' => $this->body,
            'role' => $this->role,
            'period' => $this->period,
            'highlights' => $this->highlights ?? [],
            'cover_url' => MediaUrl::for($this->cover_image),
            'live_url' => $this->live_url,
            'repo_url' => $this->repo_url,
            'status' => $this->status->value,
            'is_featured' => $this->is_featured,
            'position' => $this->position,
            'published_at' => $this->published_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn (ProjectImage $image): array => [
                'id' => $image->id,
                'url' => (string) MediaUrl::for($image->path),
                'alt' => $image->alt,
            ])->values()->all()),
        ];
    }
}
