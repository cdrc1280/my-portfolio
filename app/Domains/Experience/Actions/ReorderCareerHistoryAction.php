<?php

declare(strict_types=1);

namespace App\Domains\Experience\Actions;

use App\Domains\Experience\Models\Certificate;
use App\Domains\Experience\Models\Education;
use App\Domains\Experience\Models\Experience;
use App\Shared\Actions\ReorderAction;

/**
 * Reorders one of the career-history lists (experience, education or
 * certificates); they share identical ordering semantics.
 */
final class ReorderCareerHistoryAction
{
    public function __construct(
        private readonly ReorderAction $reorder,
    ) {}

    /**
     * @param  class-string<Experience|Education|Certificate>  $modelClass
     * @param  list<int>  $orderedIds
     */
    public function execute(string $modelClass, array $orderedIds): void
    {
        $this->reorder->execute($modelClass, $orderedIds);
    }
}
