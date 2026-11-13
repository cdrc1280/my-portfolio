<?php

declare(strict_types=1);

namespace App\Domains\Blog\Resources;

use App\Domains\Blog\Models\Post;
use App\Domains\Tags\Resources\TagResource;
use App\Shared\Support\MediaUrl;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A post in the admin list and edit form (raw Markdown body, status and schedule).
 */
#[MapName(SnakeCaseMapper::class)]
final class AdminPostResource extends Resource
{
    /**
     * @param  list<TagResource>  $tags
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public string $excerpt,
        public ?string $seoTitle,
        public ?string $seoDescription,
        public string $body,
        public ?string $coverUrl,
        public string $status,
        public bool $isScheduled,
        public ?string $publishedAt,
        public ?string $updatedAt,
        public array $tags,
    ) {}

    public static function fromModel(Post $post): self
    {
        return new self(
            id: $post->id,
            title: $post->title,
            slug: $post->slug,
            excerpt: $post->excerpt,
            seoTitle: $post->seo_title,
            seoDescription: $post->seo_description,
            body: $post->body,
            coverUrl: MediaUrl::for($post->cover_image),
            status: $post->status->value,
            isScheduled: $post->published_at?->isFuture() ?? false,
            publishedAt: $post->published_at?->toIso8601String(),
            updatedAt: $post->updated_at?->toIso8601String(),
            tags: PostCardResource::tags($post),
        );
    }
}
