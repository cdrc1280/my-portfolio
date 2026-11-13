<?php

declare(strict_types=1);

namespace App\Domains\Uses\Actions;

use App\Domains\Uses\Data\UsesItemData;
use App\Domains\Uses\Models\UsesItem;
use Illuminate\Support\Facades\DB;

final class CreateUsesItemAction
{
    public function execute(UsesItemData $data): UsesItem
    {
        return DB::transaction(fn (): UsesItem => UsesItem::query()->create(self::columns($data)));
    }

    /**
     * @return array<string, mixed>
     */
    public static function columns(UsesItemData $data): array
    {
        return [
            'category' => $data->category,
            'name' => $data->name,
            'description' => $data->description,
            'url' => $data->url,
        ];
    }
}
