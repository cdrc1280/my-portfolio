<?php

declare(strict_types=1);

namespace App\Domains\Projects\Actions;

use App\Domains\Projects\Data\ProjectData;
use App\Domains\Projects\Enums\ProjectStatus;
use App\Domains\Projects\Models\Project;
use Illuminate\Support\Facades\DB;

final class UpdateProjectAction
{
    public function __construct(
        private readonly SyncProjectMediaAction $syncMedia,
    ) {}

    public function execute(Project $project, ProjectData $data): Project
    {
        return DB::transaction(function () use ($project, $data): Project {
            $project->update([
                ...CreateProjectAction::columns($data),
                'published_at' => $this->publishedAt($project, $data->status),
            ]);

            $project->tags()->sync($data->tagIds);
            $this->syncMedia->execute($project, $data);

            return $project->load(['tags', 'images']);
        });
    }

    /**
     * Keep the original publish date across edits; reset it when unpublished.
     */
    private function publishedAt(Project $project, ProjectStatus $status): ?\DateTimeInterface
    {
        if ($status === ProjectStatus::Draft) {
            return null;
        }

        return $project->published_at ?? now();
    }
}
