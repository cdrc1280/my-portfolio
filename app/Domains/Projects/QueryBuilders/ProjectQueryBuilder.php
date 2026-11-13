<?php

declare(strict_types=1);

namespace App\Domains\Projects\QueryBuilders;

use App\Domains\Projects\Enums\ProjectStatus;
use App\Domains\Projects\Models\Project;
use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModel of Project
 *
 * @extends Builder<TModel>
 */
class ProjectQueryBuilder extends Builder
{
    /**
     * @return $this
     */
    public function published(): static
    {
        return $this->where('status', ProjectStatus::Published);
    }

    /**
     * @return $this
     */
    public function featured(): static
    {
        return $this->where('is_featured', true);
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
    public function search(?string $term): static
    {
        if ($term === null || trim($term) === '') {
            return $this;
        }

        $like = '%'.trim($term).'%';

        return $this->where(fn (Builder $query) => $query
            ->where('title', 'like', $like)
            ->orWhere('summary', 'like', $like));
    }
}
