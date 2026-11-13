<?php

declare(strict_types=1);

namespace App\Domains\Projects\Queries;

use App\Domains\Projects\Models\Project;

/**
 * The published project after the given one in display order, wrapping to the first.
 * Null when there is no other published project.
 */
final class NextProjectQuery
{
    public function execute(Project $project): ?Project
    {
        $next = Project::query()
            ->published()
            ->whereKeyNot($project->id)
            ->where(fn ($query) => $query
                ->where('position', '>', $project->position)
                ->orWhere(fn ($query) => $query->where('position', $project->position)->where('id', '>', $project->id)))
            ->ordered()
            ->first();

        return $next ?? Project::query()->published()->whereKeyNot($project->id)->ordered()->first();
    }
}
