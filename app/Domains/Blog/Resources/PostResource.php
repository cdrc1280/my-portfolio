<?php

declare(strict_types=1);

namespace App\Domains\Blog\Resources;

use App\Domains\Blog\Models\Post;
use App\Domains\Tags\Resources\TagResource;
use App\Shared\Support\Markdown;
use App\Shared\Support\MediaUrl;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A full public post, with the body rendered from Markdown on the server.
 */
#[MapName(SnakeCaseMapper::class)]
final class PostResource extends Resource
{
    /**
     * @param  list<TagResource>  $tags
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public string $excerpt,
        public ?string $coverUrl,
        public ?string $publishedAt,
        public int $readingMinutes,
        public array $tags,
        public ?string $bodyHtml,
        public ?string $seoTitle,
        public ?string $seoDescription,
        public ?string $updatedAt,
    ) {}

    public static function fromModel(Post $post): self
    {
        return new self(
            id: $post->id,
            title: $post->title,
            slug: $post->slug,
            excerpt: $post->excerpt,
            coverUrl: MediaUrl::for($post->cover_image),
            publishedAt: $post->published_at?->toIso8601String(),
            readingMinutes: Markdown::readingTime($post->body),
            tags: PostCardResource::tags($post),
            bodyHtml: Markdown::toHtml($post->body),
            seoTitle: $post->seo_title,
            seoDescription: $post->seo_description,
            updatedAt: $post->updated_at?->toIso8601String(),
        );
    }
}
