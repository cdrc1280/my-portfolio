<?php

use App\Domains\Blog\Models\Post;
use App\Domains\Contact\Models\ContactMessage;
use App\Domains\Dashboard\Queries\DashboardStatsQuery;
use App\Domains\Projects\Models\Project;

test('counts content by status and lists recent messages', function () {
    Project::factory()->published()->count(2)->create();
    Project::factory()->create();
    Post::factory()->published()->create();
    ContactMessage::factory()->count(6)->create();
    ContactMessage::factory()->read()->create();

    $stats = app(DashboardStatsQuery::class)->execute();

    expect($stats)
        ->publishedProjects->toBe(2)
        ->draftProjects->toBe(1)
        ->publishedPosts->toBe(1)
        ->draftPosts->toBe(0)
        ->unreadMessages->toBe(6)
        ->totalMessages->toBe(7)
        ->and($stats->recentMessages)->toHaveCount(5);
});
