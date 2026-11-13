<?php

declare(strict_types=1);

namespace App\Domains\Uses\Actions;

use App\Domains\Uses\Models\UsesItem;
use App\Shared\Actions\ReorderAction;

final class ReorderUsesItemsAction
{
    public function __construct(
        private readonly ReorderAction $reorder,
    ) {}

    /**
     * @param  list<int>  $orderedIds
     */
    public function execute(array $orderedIds): void
    {
        $this->reorder->execute(UsesItem::class, $orderedIds);
    }
}
