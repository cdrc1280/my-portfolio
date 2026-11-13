<?php

declare(strict_types=1);

namespace App\Domains\Uses\Actions;

use App\Domains\Uses\Models\UsesItem;

final class DeleteUsesItemAction
{
    public function execute(UsesItem $usesItem): void
    {
        $usesItem->delete();
    }
}
