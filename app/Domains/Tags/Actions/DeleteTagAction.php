<?php

declare(strict_types=1);

namespace App\Domains\Tags\Actions;

use App\Domains\Tags\Models\Tag;

final class DeleteTagAction
{
    /**
     * Pivot rows are removed by the foreign key cascade.
     */
    public function execute(Tag $tag): void
    {
        $tag->delete();
    }
}
