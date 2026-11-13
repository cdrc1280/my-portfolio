<?php

use App\Domains\Tags\Actions\UpdateTagAction;
use App\Domains\Tags\Data\TagData;
use App\Domains\Tags\Models\Tag;

test('updates a tag', function () {
    $tag = Tag::factory()->create(['name' => 'Vue', 'slug' => 'vue']);

    app(UpdateTagAction::class)->execute($tag, TagData::from(['name' => 'Vue.js', 'slug' => 'vuejs']));

    expect($tag->fresh())->name->toBe('Vue.js')->slug->toBe('vuejs');
});
