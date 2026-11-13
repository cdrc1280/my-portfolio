<?php

declare(strict_types=1);

namespace App\Domains\Projects\Resources;

use App\Domains\Projects\Models\Project;
use App\Domains\Tags\Models\Tag;
use App\Domains\Tags\Resources\TagResource;
use App\Shared\Support\MediaUrl;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A project card on the home page and the projects grid.
 */
#[MapName(SnakeCaseMapper::class)]
final class ProjectCardResource extends Resource
{
    /**
     * @param  list<TagResource>  $tags
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public string $summary,
        public ?string $role,
        public ?string $startedOn,
        public ?string $endedOn,
        public ?string $coverUrl,
        public ?string $liveUrl,
        public ?string $repoUrl,
        public bool $isConfidential,
        public bool $isFeatured,
        public array $tags,
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
            coverUrl: MediaUrl::for($project->cover_image),
            liveUrl: $project->live_url,
            repoUrl: $project->publicRepoUrl(),
            isConfidential: $project->is_confidential,
            isFeatured: $project->is_featured,
            tags: self::tags($project),
        );
    }

    /**
     * @return list<TagResource>
     */
    public static function tags(Project $project): array
    {
        return $project->relationLoaded('tags')
            ? array_values($project->tags->map(fn (Tag $tag): TagResource => TagResource::fromModel($tag))->all())
            : [];
    }

    /**
     * Gallery images; public pages fall back to the project title for missing alt text.
     *
     * @return list<array{id: int, url: string, alt: string|null}>
     */
    public static function images(Project $project, bool $altFallback): array
    {
        if (! $project->relationLoaded('images')) {
            return [];
        }

        return array_values($project->images->map(fn ($image): array => [
            'id' => $image->id,
            'url' => (string) MediaUrl::for($image->path),
            'alt' => $image->alt ?? ($altFallback ? $project->title : null),
        ])->all());
    }
}
