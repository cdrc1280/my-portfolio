<?php

declare(strict_types=1);

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Certificate;
use App\Domain\Experience\Models\Education;
use App\Domain\Experience\Models\Experience;
use App\Domain\Shared\Actions\ReorderAction;

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
