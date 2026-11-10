<?php

use App\Domain\Tags\Actions\UpdateTagAction;
use App\Domain\Tags\DataTransferObjects\TagData;
use App\Domain\Tags\Models\Tag;

test('updates a tag', function () {
    $tag = Tag::factory()->create(['name' => 'Vue', 'slug' => 'vue']);

    app(UpdateTagAction::class)->execute($tag, TagData::fromArray(['name' => 'Vue.js', 'slug' => 'vuejs']));

    expect($tag->fresh())->name->toBe('Vue.js')->slug->toBe('vuejs');
});
