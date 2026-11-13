<?php

use App\Domains\Tags\Actions\ReorderTagsAction;
use App\Domains\Tags\Models\Tag;

test('tags can be reordered', function () {
    [$a, $b] = Tag::factory()->count(2)->create();

    app(ReorderTagsAction::class)->execute([$b->id, $a->id]);

    expect($b->fresh()->position)->toBe(1);
});
