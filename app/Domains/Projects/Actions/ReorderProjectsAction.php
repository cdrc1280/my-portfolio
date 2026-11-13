<?php

declare(strict_types=1);

namespace App\Domains\Projects\Actions;

use App\Domains\Projects\Models\Project;
use App\Shared\Actions\ReorderAction;

final class ReorderProjectsAction
{
    public function __construct(
        private readonly ReorderAction $reorder,
    ) {}

    /**
     * @param  list<int>  $orderedIds
     */
    public function execute(array $orderedIds): void
    {
        $this->reorder->execute(Project::class, $orderedIds);
    }
}
