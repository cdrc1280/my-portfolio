<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Projects\Models\Project;
use App\Domain\Projects\Models\ProjectImage;
use App\Domain\Shared\Support\Markdown;
use App\Domain\Shared\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Full public project detail.
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
            'body_html' => Markdown::toHtml($this->body),
            'role' => $this->role,
            'period' => $this->period,
            'highlights' => $this->highlights ?? [],
            'cover_url' => MediaUrl::for($this->cover_image),
            'live_url' => $this->live_url,
            'repo_url' => $this->repo_url,
            'published_at' => $this->published_at?->toIso8601String(),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn (ProjectImage $image): array => [
                'id' => $image->id,
                'url' => (string) MediaUrl::for($image->path),
                'alt' => $image->alt ?? $this->title,
            ])->values()->all()),
        ];
    }
}
