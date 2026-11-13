<?php

declare(strict_types=1);

namespace App\Domains\Projects\Resources;

use App\Domains\Projects\Models\Project;
use App\Domains\Projects\Support\FolderTree;
use App\Domains\Tags\Resources\TagResource;
use App\Shared\Support\MediaUrl;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A project in the admin list and edit form: raw Markdown, the folder tree as editable text,
 * status, flags and media.
 */
#[MapName(SnakeCaseMapper::class)]
final class AdminProjectResource extends Resource
{
    /**
     * @param  list<array{label: string, value: string}>  $metrics
     * @param  list<TagResource>  $tags
     * @param  list<array{id: int, url: string, alt: string|null}>  $images
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public string $summary,
        public ?string $problem,
        public ?string $role,
        public ?string $roleDetails,
        public ?string $architecture,
        public string $architectureTree,
        public ?string $diagramUrl,
        public ?string $decisions,
        public ?string $challenges,
        public ?string $results,
        public array $metrics,
        public ?string $coverUrl,
        public ?string $liveUrl,
        public ?string $repoUrl,
        public ?string $demoUrl,
        public bool $isConfidential,
        public ?string $startedOn,
        public ?string $endedOn,
        public string $status,
        public bool $isFeatured,
        public ?string $seoTitle,
        public ?string $seoDescription,
        public int $position,
        public ?string $publishedAt,
        public ?string $updatedAt,
        public array $tags,
        public array $images,
    ) {}

    public static function fromModel(Project $project): self
    {
        return new self(
            id: $project->id,
            title: $project->title,
            slug: $project->slug,
            summary: $project->summary,
            problem: $project->problem,
            role: $project->role,
            roleDetails: $project->role_details,
            architecture: $project->architecture,
            architectureTree: FolderTree::format($project->architecture_tree ?? []),
            diagramUrl: MediaUrl::for($project->diagram_path),
            decisions: $project->decisions,
            challenges: $project->challenges,
            results: $project->results,
            metrics: $project->metrics ?? [],
            coverUrl: MediaUrl::for($project->cover_image),
            liveUrl: $project->live_url,
            repoUrl: $project->repo_url,
            demoUrl: $project->demo_url,
            isConfidential: $project->is_confidential,
            startedOn: $project->started_on?->format('Y-m'),
            endedOn: $project->ended_on?->format('Y-m'),
            status: $project->status->value,
            isFeatured: $project->is_featured,
            seoTitle: $project->seo_title,
            seoDescription: $project->seo_description,
            position: $project->position,
            publishedAt: $project->published_at?->toIso8601String(),
            updatedAt: $project->updated_at?->toIso8601String(),
            tags: ProjectCardResource::tags($project),
            images: ProjectCardResource::images($project, altFallback: false),
        );
    }
}
