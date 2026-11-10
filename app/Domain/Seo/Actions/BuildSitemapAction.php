<?php

declare(strict_types=1);

namespace App\Domain\Seo\Actions;

use App\Domain\Blog\Models\Post;
use App\Domain\Projects\Models\Project;
use App\Domain\Seo\ValueObjects\SitemapEntry;

final class BuildSitemapAction
{
    /**
     * @return list<SitemapEntry>
     */
    public function execute(): array
    {
        $entries = [
            new SitemapEntry(route('home'), priority: 1.0),
            new SitemapEntry(route('projects.index'), priority: 0.9),
            new SitemapEntry(route('about'), priority: 0.8),
            new SitemapEntry(route('blog.index'), priority: 0.7),
            new SitemapEntry(route('contact.show'), priority: 0.5),
        ];

        foreach (Project::query()->published()->ordered()->get(['slug', 'updated_at']) as $project) {
            $entries[] = new SitemapEntry(route('projects.show', $project->slug), $project->updated_at, 0.8);
        }

        foreach (Post::query()->published()->newestFirst()->get(['slug', 'updated_at']) as $post) {
            $entries[] = new SitemapEntry(route('blog.show', $post->slug), $post->updated_at, 0.6);
        }

        return $entries;
    }
}
