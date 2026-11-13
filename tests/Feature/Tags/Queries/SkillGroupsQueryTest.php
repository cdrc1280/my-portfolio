<?php

use App\Domains\Tags\Enums\TagCategory;
use App\Domains\Tags\Models\Tag;
use App\Domains\Tags\Queries\SkillGroupsQuery;

test('skill groups follow category order, skip empty categories and ignore uncategorized tags', function () {
    Tag::factory()->skill(TagCategory::AiTools)->create(['name' => 'Claude Code']);
    Tag::factory()->skill(TagCategory::Backend)->create(['name' => 'PHP', 'position' => 2]);
    Tag::factory()->skill(TagCategory::Backend)->create(['name' => 'Laravel', 'position' => 1]);
    Tag::factory()->create(['name' => 'Misc']);

    $groups = app(SkillGroupsQuery::class)->execute();

    expect(array_column($groups, 'category'))->toBe(['backend', 'ai_tools'])
        ->and(array_column($groups[0]['skills'], 'name'))->toBe(['Laravel', 'PHP']);
});
