<?php

declare(strict_types=1);

namespace App\Domains\Seo\Queries;

use App\Domains\Blog\Models\Post;
use App\Domains\Profile\Actions\GetProfileAction;
use App\Domains\Projects\Models\Project;
use App\Domains\Seo\ValueObjects\SitemapEntry;
use App\Domains\Services\Models\Service;
use App\Domains\Uses\Models\UsesItem;

final class SitemapQuery
{
    public function __construct(
        private readonly GetProfileAction $getProfile,
    ) {}

    /**
     * @return list<SitemapEntry>
     */
    public function execute(): array
    {
        $blogEnabled = $this->getProfile->snapshot()->blogEnabled;

        $entries = [
            new SitemapEntry(route('home'), priority: 1.0),
            new SitemapEntry(route('projects.index'), priority: 0.9),
            new SitemapEntry(route('about'), priority: 0.8),
            new SitemapEntry(route('contact.show'), priority: 0.7),
        ];

        // Optional pages are listed only when they have content.
        if (Service::query()->exists()) {
            $entries[] = new SitemapEntry(route('services'), priority: 0.8);
        }

        if (UsesItem::query()->exists()) {
            $entries[] = new SitemapEntry(route('uses'), priority: 0.4);
        }

        if ($blogEnabled) {
            $entries[] = new SitemapEntry(route('blog.index'), priority: 0.6);
        }

        foreach (Project::query()->published()->ordered()->get(['slug', 'updated_at']) as $project) {
            $entries[] = new SitemapEntry(route('projects.show', $project->slug), $project->updated_at, 0.8);
        }

        $posts = $blogEnabled ? Post::query()->published()->newestFirst()->get(['slug', 'updated_at']) : [];

        foreach ($posts as $post) {
            $entries[] = new SitemapEntry(route('blog.show', $post->slug), $post->updated_at, 0.6);
        }

        return $entries;
    }
}
