<?php

declare(strict_types=1);

namespace App\Domains\Projects\Actions;

use App\Domains\Projects\Data\ProjectData;
use App\Domains\Projects\Enums\ProjectStatus;
use App\Domains\Projects\Models\Project;
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
                ...self::columns($data),
                'published_at' => $data->status === ProjectStatus::Published ? now() : null,
            ]);

            $project->tags()->sync($data->tagIds);
            $this->syncMedia->execute($project, $data);

            return $project->load(['tags', 'images']);
        });
    }

    /**
     * Column values for the projects table (files and relations are handled separately).
     *
     * @return array<string, mixed>
     */
    public static function columns(ProjectData $data): array
    {
        return [
            'title' => $data->title,
            'slug' => $data->slug,
            'summary' => $data->summary,
            'problem' => $data->problem,
            'role' => $data->role,
            'role_details' => $data->roleDetails,
            'architecture' => $data->architecture,
            'architecture_tree' => $data->folderTree() ?: null,
            'decisions' => $data->decisions,
            'challenges' => $data->challenges,
            'results' => $data->results,
            'metrics' => $data->completeMetrics() ?: null,
            'live_url' => $data->liveUrl,
            'repo_url' => $data->repoUrl,
            'demo_url' => $data->demoUrl,
            'is_confidential' => $data->isConfidential,
            'started_on' => $data->startedOn,
            'ended_on' => $data->endedOn,
            'status' => $data->status,
            'is_featured' => $data->isFeatured,
            'seo_title' => $data->seoTitle,
            'seo_description' => $data->seoDescription,
        ];
    }
}
