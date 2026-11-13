<?php

declare(strict_types=1);

namespace App\Domains\Tags\Actions;

use App\Domains\Tags\Models\Tag;
use App\Shared\Actions\ReorderAction;

final class ReorderTagsAction
{
    public function __construct(
        private readonly ReorderAction $reorder,
    ) {}

    /**
     * @param  list<int>  $orderedIds
     */
    public function execute(array $orderedIds): void
    {
        $this->reorder->execute(Tag::class, $orderedIds);
    }
}
