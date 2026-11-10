<?php

declare(strict_types=1);

namespace App\Domain\Tags\Actions;

use App\Domain\Tags\DataTransferObjects\TagData;
use App\Domain\Tags\Models\Tag;

final class UpdateTagAction
{
    public function execute(Tag $tag, TagData $data): Tag
    {
        $tag->update($data->toArray());

        return $tag;
    }
}
