<?php

declare(strict_types=1);

namespace App\Domains\Projects\Resources;

use App\Domains\Projects\Models\Project;
use App\Domains\Tags\Resources\TagResource;
use App\Shared\Support\Markdown;
use App\Shared\Support\MediaUrl;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A full public case study, with every Markdown section rendered on the server.
 */
#[MapName(SnakeCaseMapper::class)]
final class ProjectResource extends Resource
{
    /**
     * @param  list<array<string, mixed>>  $architectureTree
     * @param  list<array{label: string, value: string}>  $metrics
     * @param  list<TagResource>  $tags
     * @param  list<array{id: int, url: string, alt: string|null}>  $images
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public string $summary,
        public ?string $role,
        public ?string $startedOn,
        public ?string $endedOn,
        public ?string $problemHtml,
        public ?string $roleHtml,
        public ?string $architectureHtml,
        public array $architectureTree,
        public ?string $diagramUrl,
        public ?string $decisionsHtml,
        public ?string $challengesHtml,
        public ?string $resultsHtml,
        public array $metrics,
        public ?string $coverUrl,
        public ?string $liveUrl,
        public ?string $repoUrl,
        public ?string $demoUrl,
        public bool $isConfidential,
        public ?string $publishedAt,
        public ?string $seoTitle,
        public ?string $seoDescription,
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
            role: $project->role,
            startedOn: $project->started_on?->format('Y-m'),
            endedOn: $project->ended_on?->format('Y-m'),
            problemHtml: Markdown::toHtml($project->problem),
            roleHtml: Markdown::toHtml($project->role_details),
            architectureHtml: Markdown::toHtml($project->architecture),
            architectureTree: $project->architecture_tree ?? [],
            diagramUrl: MediaUrl::for($project->diagram_path),
            decisionsHtml: Markdown::toHtml($project->decisions),
            challengesHtml: Markdown::toHtml($project->challenges),
            resultsHtml: Markdown::toHtml($project->results),
            metrics: $project->metrics ?? [],
            coverUrl: MediaUrl::for($project->cover_image),
            liveUrl: $project->live_url,
            repoUrl: $project->publicRepoUrl(),
            demoUrl: $project->demo_url,
            isConfidential: $project->is_confidential,
            publishedAt: $project->published_at?->toIso8601String(),
            seoTitle: $project->seo_title,
            seoDescription: $project->seo_description,
            tags: ProjectCardResource::tags($project),
            images: ProjectCardResource::images($project, altFallback: true),
        );
    }
}
