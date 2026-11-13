<?php

declare(strict_types=1);

namespace App\Domains\Projects\Queries;

use App\Domains\Projects\Enums\ProjectStatus;
use App\Domains\Projects\Models\Project;
use App\Domains\Projects\Resources\AdminProjectResource;
use App\Domains\Projects\Resources\ProjectCardResource;
use App\Domains\Tags\Models\Tag;
use App\Domains\Tags\Resources\TagResource;
use Illuminate\Database\Eloquent\Builder;

/**
 * Read side of the Projects module.
 */
final class ProjectQueries
{
    /**
     * @return list<AdminProjectResource>
     */
    public function forAdmin(): array
    {
        return array_values(Project::query()->with('tags')->ordered()->get()
            ->map(fn (Project $project): AdminProjectResource => AdminProjectResource::fromModel($project))
            ->all());
    }

    /**
     * Every project as a select option (value = id as a string).
     *
     * @return list<array{value: string, label: string}>
     */
    public function allAsOptions(): array
    {
        return array_values(Project::query()->ordered()->get(['id', 'title'])
            ->map(fn (Project $project): array => ['value' => (string) $project->id, 'label' => $project->title])
            ->all());
    }

    /**
     * Up to three featured, published projects for the home page.
     *
     * @return list<ProjectCardResource>
     */
    public function featuredForHome(): array
    {
        return $this->cards(Project::query()->published()->featured()->with('tags')->ordered()->limit(3));
    }

    /**
     * Published projects in manual order, optionally only those with a tag.
     *
     * @return list<ProjectCardResource>
     */
    public function published(?string $tag): array
    {
        return $this->cards(Project::query()->published()->withTag($tag)->with('tags')->ordered());
    }

    /**
     * Tags that would return at least one published project, for the filter.
     *
     * @return list<TagResource>
     */
    public function filterTags(): array
    {
        return array_values(Tag::query()
            ->whereHas('projects', fn (Builder $query) => $query->where('status', ProjectStatus::Published))
            ->orderBy('name')
            ->get()
            ->map(fn (Tag $tag): TagResource => TagResource::fromModel($tag))
            ->all());
    }

    public function findPublished(string $slug): Project
    {
        return Project::query()->published()->where('slug', $slug)->with(['tags', 'images'])->firstOrFail();
    }

    /**
     * @param  Builder<Project>  $query
     * @return list<ProjectCardResource>
     */
    private function cards(Builder $query): array
    {
        return array_values($query->get()
            ->map(fn (Project $project): ProjectCardResource => ProjectCardResource::fromModel($project))
            ->all());
    }
}
