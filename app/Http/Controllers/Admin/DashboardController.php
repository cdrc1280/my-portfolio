<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Dashboard\Actions\GetDashboardStatsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ContactMessageResource;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(GetDashboardStatsAction $getStats): Response
    {
        $stats = $getStats->execute();

        return Inertia::render('admin/dashboard', [
            'stats' => [
                'published_projects' => $stats->publishedProjects,
                'draft_projects' => $stats->draftProjects,
                'published_posts' => $stats->publishedPosts,
                'draft_posts' => $stats->draftPosts,
                'skills' => $stats->skills,
                'unread_messages' => $stats->unreadMessages,
                'total_messages' => $stats->totalMessages,
            ],
            'recentMessages' => ContactMessageResource::collection($stats->recentMessages),
        ]);
    }
}
