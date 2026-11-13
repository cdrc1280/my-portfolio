<?php

declare(strict_types=1);

namespace App\Domains\Tags\Resources;

use App\Domains\Tags\Models\Tag;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A tag in the admin list, with its skill category and how often it is used.
 */
#[MapName(SnakeCaseMapper::class)]
final class AdminTagResource extends Resource
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public ?string $category,
        public ?string $categoryLabel,
        public ?string $icon,
        public int $position,
        public int $projectsCount,
        public int $postsCount,
    ) {}

    public static function fromModel(Tag $tag): self
    {
        return new self(
            id: $tag->id,
            name: $tag->name,
            slug: $tag->slug,
            category: $tag->category?->value,
            categoryLabel: $tag->category?->label(),
            icon: $tag->icon,
            position: $tag->position,
            projectsCount: (int) ($tag->projects_count ?? 0),
            postsCount: (int) ($tag->posts_count ?? 0),
        );
    }
}
