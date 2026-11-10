<?php

declare(strict_types=1);

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\DataTransferObjects\ProjectData;
use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
use Illuminate\Support\Facades\DB;

final class CreateProjectAction
{
    public function __construct(
        private readonly SyncProjectMediaAction $syncMedia,
    ) {}

    public function execute(ProjectData $data): Project
    {
        return DB::transaction(function () use ($data): Project {
            $project = Project::query()->create([
                ...$data->attributes(),
                'published_at' => $data->status === ProjectStatus::Published ? now() : null,
            ]);

            $project->tags()->sync($data->tagIds);
            $this->syncMedia->execute($project, $data);

            return $project->load(['tags', 'images']);
        });
    }
}
