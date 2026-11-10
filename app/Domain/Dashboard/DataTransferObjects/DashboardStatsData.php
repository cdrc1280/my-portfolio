<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\DataTransferObjects;

use App\Domain\Contact\Models\ContactMessage;
use Illuminate\Support\Collection;

final readonly class DashboardStatsData
{
    /**
     * @param  Collection<int, ContactMessage>  $recentMessages
     */
    public function __construct(
        public int $publishedProjects,
        public int $draftProjects,
        public int $publishedPosts,
        public int $draftPosts,
        public int $skills,
        public int $unreadMessages,
        public int $totalMessages,
        public Collection $recentMessages,
    ) {}
}
