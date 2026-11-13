<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Controllers\Admin;

use App\Domains\Bookings\Actions\GetUpcomingBookingsAction;
use App\Domains\Contact\Models\ContactMessage;
use App\Domains\Contact\Resources\ContactMessageResource;
use App\Domains\Dashboard\Queries\ContentHealthQuery;
use App\Domains\Dashboard\Queries\DashboardStatsQuery;
use App\Domains\Dashboard\Queries\LeadStatsQuery;
use App\Domains\GitHub\Actions\GetGitHubActivityAction;
use App\Domains\Profile\Actions\GetProfileAction;
use App\Domains\Profile\Enums\AvailabilityStatus;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Bento dashboard. Local numbers render immediately; Calendly and GitHub are deferred so a slow
 * external API never blocks the page.
 */
class DashboardController extends Controller
{
    public function __invoke(
        DashboardStatsQuery $getStats,
        LeadStatsQuery $getLeads,
        ContentHealthQuery $getHealth,
        GetProfileAction $getProfile,
    ): Response {
        $stats = $getStats->execute();
        $profile = $getProfile->execute();

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
            'recentMessages' => $stats->recentMessages->map(fn (ContactMessage $message): ContactMessageResource => ContactMessageResource::fromModel($message))->values()->all(),
            'leads' => $getLeads->execute(),
            'health' => $getHealth->execute(),
            'availability' => [
                'status' => $profile->availability_status->value,
                'note' => $profile->availability,
                'options' => AvailabilityStatus::options(),
            ],
            'bookings' => Inertia::defer(fn (): array => app(GetUpcomingBookingsAction::class)->execute(), 'external'),
            'github' => Inertia::defer(fn (): ?array => app(GetGitHubActivityAction::class)->execute()?->toArray(), 'external'),
        ]);
    }
}
