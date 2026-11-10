<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Actions;

use App\Domain\Blog\Enums\PostStatus;
use App\Domain\Blog\Models\Post;
use App\Domain\Contact\Models\ContactMessage;
use App\Domain\Dashboard\DataTransferObjects\DashboardStatsData;
use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
use App\Domain\Skills\Models\Skill;

final class GetDashboardStatsAction
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
            skills: Skill::query()->count(),
            unreadMessages: ContactMessage::query()->unread()->count(),
            totalMessages: ContactMessage::query()->count(),
            recentMessages: ContactMessage::query()->latest()->limit(5)->get(),
        );
    }
}
