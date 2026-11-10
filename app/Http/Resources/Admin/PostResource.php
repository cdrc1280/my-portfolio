<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Domain\Blog\Models\Post;
use App\Domain\Shared\Support\MediaUrl;
use App\Http\Resources\TagResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class PostResource extends JsonResource
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
            'body' => $this->body,
            'cover_url' => MediaUrl::for($this->cover_image),
            'status' => $this->status->value,
            'is_scheduled' => $this->published_at?->isFuture() ?? false,
            'published_at' => $this->published_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
