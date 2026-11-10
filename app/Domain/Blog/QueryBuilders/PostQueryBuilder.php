<?php

declare(strict_types=1);

namespace App\Domain\Blog\QueryBuilders;

use App\Domain\Blog\Enums\PostStatus;
use App\Domain\Blog\Models\Post;
use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModel of Post
 *
 * @extends Builder<TModel>
 */
class PostQueryBuilder extends Builder
{
    /**
     * Published and not scheduled for later.
     *
     * @return $this
     */
    public function published(): static
    {
        return $this->where('status', PostStatus::Published)->where('published_at', '<=', now());
    }

    /**
     * @return $this
     */
    public function withTag(?string $slug): static
    {
        if ($slug === null || $slug === '') {
            return $this;
        }

        return $this->whereHas('tags', fn (Builder $query) => $query->where('slug', $slug));
    }

    /**
     * @return $this
     */
    public function newestFirst(): static
    {
        return $this->orderByDesc('published_at')->orderByDesc('id');
    }
}
