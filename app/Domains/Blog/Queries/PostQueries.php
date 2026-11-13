<?php

declare(strict_types=1);

namespace App\Domains\Blog\Queries;

use App\Domains\Blog\Models\Post;
use App\Domains\Blog\Resources\AdminPostResource;
use App\Domains\Blog\Resources\PostCardResource;
use App\Domains\Blog\Resources\PostResource;
use App\Shared\Support\Paginated;

/**
 * Read side of the Blog module.
 */
final class PostQueries
{
    /**
     * Admin list: drafts first, then newest publish date.
     *
     * @return array<string, mixed>
     */
    public function paginateForAdmin(): array
    {
        $posts = Post::query()
            ->with('tags')
            ->orderByRaw('published_at is null desc')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(20);

        return Paginated::from($posts, fn (Post $post): AdminPostResource => AdminPostResource::fromModel($post));
    }

    /**
     * Published posts, newest first, optionally only those with a tag.
     *
     * @return array<string, mixed>
     */
    public function paginatePublished(?string $tag): array
    {
        $posts = Post::query()->published()->withTag($tag)->with('tags')->newestFirst()->paginate(10)->withQueryString();

        return Paginated::from($posts, fn (Post $post): PostCardResource => PostCardResource::fromModel($post));
    }

    public function findPublished(string $slug): PostResource
    {
        return PostResource::fromModel(Post::query()->published()->where('slug', $slug)->with('tags')->firstOrFail());
    }
}
