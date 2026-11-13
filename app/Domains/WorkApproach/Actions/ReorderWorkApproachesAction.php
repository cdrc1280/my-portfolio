<?php

declare(strict_types=1);

namespace App\Domains\WorkApproach\Actions;

use App\Domains\WorkApproach\Models\WorkApproach;
use App\Shared\Actions\ReorderAction;

final class ReorderWorkApproachesAction
{
    public function __construct(
        private readonly ReorderAction $reorder,
    ) {}

    /**
     * @param  list<int>  $orderedIds
     */
    public function execute(array $orderedIds): void
    {
        $this->reorder->execute(WorkApproach::class, $orderedIds);
    }
}
