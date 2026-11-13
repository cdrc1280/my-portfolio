<?php

declare(strict_types=1);

namespace App\Domains\Tags\Queries;

use App\Domains\Tags\Models\Tag;
use App\Domains\Tags\Resources\AdminTagResource;
use App\Domains\Tags\Resources\TagResource;

/**
 * Read side of the Tags module.
 */
final class TagQueries
{
    /**
     * Every tag in manual order, with usage counts, for the admin list.
     *
     * @return list<AdminTagResource>
     */
    public function forAdmin(): array
    {
        return array_values(Tag::query()->withCount(['projects', 'posts'])->ordered()->get()
            ->map(fn (Tag $tag): AdminTagResource => AdminTagResource::fromModel($tag))
            ->all());
    }

    /**
     * Every tag, alphabetically, for tag pickers in forms.
     *
     * @return list<TagResource>
     */
    public function allAsOptions(): array
    {
        return array_values(Tag::query()->orderBy('name')->get()
            ->map(fn (Tag $tag): TagResource => TagResource::fromModel($tag))
            ->all());
    }
}
