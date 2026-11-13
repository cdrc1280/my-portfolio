<?php

declare(strict_types=1);

namespace App\Domains\WorkApproach\Queries;

use App\Domains\WorkApproach\Models\WorkApproach;
use App\Domains\WorkApproach\Resources\WorkApproachResource;

/**
 * Read side of the WorkApproach module.
 */
final class WorkApproachQueries
{
    /**
     * Every principle in manual order.
     *
     * @return list<WorkApproachResource>
     */
    public function ordered(): array
    {
        return array_values(WorkApproach::query()->ordered()->get()
            ->map(fn (WorkApproach $item): WorkApproachResource => WorkApproachResource::fromModel($item))
            ->all());
    }
}
