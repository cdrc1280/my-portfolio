<?php

declare(strict_types=1);

namespace App\Domain\Skills\Actions;

use App\Domain\Shared\Actions\ReorderAction;
use App\Domain\Skills\Models\SkillCategory;

final class ReorderSkillCategoriesAction
{
    public function __construct(
        private readonly ReorderAction $reorder,
    ) {}

    /**
     * @param  list<int>  $orderedIds
     */
    public function execute(array $orderedIds): void
    {
        $this->reorder->execute(SkillCategory::class, $orderedIds);
    }
}
