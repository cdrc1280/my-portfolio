<?php

declare(strict_types=1);

namespace App\Domain\Tags\Actions;

use App\Domain\Tags\DataTransferObjects\TagData;
use App\Domain\Tags\Models\Tag;

final class CreateTagAction
{
    public function execute(TagData $data): Tag
    {
        return Tag::query()->create($data->toArray());
    }
}
