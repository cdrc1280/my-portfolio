<?php

declare(strict_types=1);

namespace App\Domain\Skills\Actions;

use App\Domain\Shared\Actions\ReorderAction;
use App\Domain\Skills\Models\Skill;

final class ReorderSkillsAction
{
    public function __construct(
        private readonly ReorderAction $reorder,
    ) {}

    /**
     * Skills are ordered within their category; pass one category's ids.
     *
     * @param  list<int>  $orderedIds
     */
    public function execute(array $orderedIds): void
    {
        $this->reorder->execute(Skill::class, $orderedIds);
    }
}
