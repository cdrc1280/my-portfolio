<?php

declare(strict_types=1);

namespace App\Domains\Uses\Queries;

use App\Domains\Uses\Models\UsesItem;
use App\Domains\Uses\Resources\UsesItemResource;

/**
 * Read side of the Uses module.
 */
final class UsesItemQueries
{
    /**
     * Every item in manual order.
     *
     * @return list<UsesItemResource>
     */
    public function ordered(): array
    {
        return array_values(UsesItem::query()->ordered()->get()
            ->map(fn (UsesItem $item): UsesItemResource => UsesItemResource::fromModel($item))
            ->all());
    }
}
