<?php

declare(strict_types=1);

namespace App\Domains\Tags\Actions;

use App\Domains\Tags\Data\TagData;
use App\Domains\Tags\Enums\TagCategory;
use App\Domains\Tags\Models\Tag;
use Illuminate\Support\Facades\DB;

final class CreateTagAction
{
    public function execute(TagData $data): Tag
    {
        return DB::transaction(fn (): Tag => Tag::query()->create(self::columns($data)));
    }

    /**
     * @return array{name: string, slug: string, category: TagCategory|null, icon: string|null}
     */
    public static function columns(TagData $data): array
    {
        return [
            'name' => $data->name,
            'slug' => $data->slug,
            'category' => $data->category,
            'icon' => $data->icon,
        ];
    }
}
