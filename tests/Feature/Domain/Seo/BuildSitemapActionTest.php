<?php

use App\Domain\Blog\Models\Post;
use App\Domain\Projects\Models\Project;
use App\Domain\Seo\Actions\BuildSitemapAction;

test('includes static pages and only published content', function () {
    Project::factory()->published()->create(['slug' => 'live-project']);
    Project::factory()->create(['slug' => 'draft-project']);
    Post::factory()->published()->create(['slug' => 'live-post']);
    Post::factory()->scheduled()->create(['slug' => 'future-post']);

    $urls = array_map(fn ($entry) => $entry->url, app(BuildSitemapAction::class)->execute());

    expect($urls)
        ->toContain(route('home'), route('about'), route('projects.show', 'live-project'), route('blog.show', 'live-post'))
        ->not->toContain(route('projects.show', 'draft-project'), route('blog.show', 'future-post'));
});
