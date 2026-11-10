<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Blog\Models\Post;
use App\Domain\Shared\Support\Markdown;
use Illuminate\Http\Request;

/**
 * @mixin Post
 */
class PostResource extends PostCardResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'body_html' => Markdown::toHtml($this->body),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
