<?php

declare(strict_types=1);

namespace App\Domains\Services\Queries;

use App\Domains\Services\Models\Service;
use App\Domains\Services\Resources\ServiceResource;

/**
 * Read side of the Services module.
 */
final class ServiceQueries
{
    /**
     * Services in manual order, optionally only the first few.
     *
     * @return list<ServiceResource>
     */
    public function ordered(?int $limit = null): array
    {
        return array_values(Service::query()->ordered()->when($limit, fn ($query, int $limit) => $query->limit($limit))->get()
            ->map(fn (Service $item): ServiceResource => ServiceResource::fromModel($item))
            ->all());
    }
}
