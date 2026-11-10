<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Blog\Models\Post;
use App\Domain\Shared\Support\Markdown;
use App\Domain\Shared\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class PostCardResource extends JsonResource
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
            'excerpt' => $this->excerpt,
            'cover_url' => MediaUrl::for($this->cover_image),
            'published_at' => $this->published_at?->toIso8601String(),
            'reading_minutes' => Markdown::readingTime($this->body),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
