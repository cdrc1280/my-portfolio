<?php

declare(strict_types=1);

namespace App\Domains\Uses\Actions;

use App\Domains\Uses\Data\UsesItemData;
use App\Domains\Uses\Models\UsesItem;
use Illuminate\Support\Facades\DB;

final class UpdateUsesItemAction
{
    public function execute(UsesItem $usesItem, UsesItemData $data): UsesItem
    {
        return DB::transaction(function () use ($usesItem, $data): UsesItem {
            $usesItem->update(CreateUsesItemAction::columns($data));

            return $usesItem;
        });
    }
}
