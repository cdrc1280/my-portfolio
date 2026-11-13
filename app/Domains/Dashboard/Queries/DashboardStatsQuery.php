<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Queries;

use App\Domains\Blog\Enums\PostStatus;
use App\Domains\Blog\Models\Post;
use App\Domains\Contact\Models\ContactMessage;
use App\Domains\Dashboard\ValueObjects\DashboardStatsData;
use App\Domains\Projects\Enums\ProjectStatus;
use App\Domains\Projects\Models\Project;
use App\Domains\Tags\Models\Tag;

final class DashboardStatsQuery
{
    public function execute(): DashboardStatsData
    {
        $projects = Project::query()->toBase()->selectRaw('status, count(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');
        $posts = Post::query()->toBase()->selectRaw('status, count(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');

        return new DashboardStatsData(
            publishedProjects: (int) ($projects[ProjectStatus::Published->value] ?? 0),
            draftProjects: (int) ($projects[ProjectStatus::Draft->value] ?? 0),
            publishedPosts: (int) ($posts[PostStatus::Published->value] ?? 0),
            draftPosts: (int) ($posts[PostStatus::Draft->value] ?? 0),
            skills: Tag::query()->whereNotNull('category')->count(),
            unreadMessages: ContactMessage::query()->unread()->count(),
            totalMessages: ContactMessage::query()->count(),
            recentMessages: ContactMessage::query()->latest()->limit(5)->get(),
        );
    }
}
