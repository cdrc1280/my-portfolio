<?php

declare(strict_types=1);

namespace App\Domains\Tags\Actions;

use App\Domains\Tags\Data\TagData;
use App\Domains\Tags\Models\Tag;
use Illuminate\Support\Facades\DB;

final class UpdateTagAction
{
    public function execute(Tag $tag, TagData $data): Tag
    {
        return DB::transaction(function () use ($tag, $data): Tag {
            $tag->update(CreateTagAction::columns($data));

            return $tag;
        });
    }
}
