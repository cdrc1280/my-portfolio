<?php

declare(strict_types=1);

namespace App\Domains\Services\Actions;

use App\Domains\Services\Models\Service;
use App\Shared\Actions\ReorderAction;

final class ReorderServicesAction
{
    public function __construct(
        private readonly ReorderAction $reorder,
    ) {}

    /**
     * @param  list<int>  $orderedIds
     */
    public function execute(array $orderedIds): void
    {
        $this->reorder->execute(Service::class, $orderedIds);
    }
}
