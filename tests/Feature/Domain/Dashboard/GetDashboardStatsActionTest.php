<?php

use App\Domain\Blog\Models\Post;
use App\Domain\Contact\Models\ContactMessage;
use App\Domain\Dashboard\Actions\GetDashboardStatsAction;
use App\Domain\Projects\Models\Project;

test('counts content by status and lists recent messages', function () {
    Project::factory()->published()->count(2)->create();
    Project::factory()->create();
    Post::factory()->published()->create();
    ContactMessage::factory()->count(6)->create();
    ContactMessage::factory()->read()->create();

    $stats = app(GetDashboardStatsAction::class)->execute();

    expect($stats)
        ->publishedProjects->toBe(2)
        ->draftProjects->toBe(1)
        ->publishedPosts->toBe(1)
        ->draftPosts->toBe(0)
        ->unreadMessages->toBe(6)
        ->totalMessages->toBe(7)
        ->and($stats->recentMessages)->toHaveCount(5);
});
