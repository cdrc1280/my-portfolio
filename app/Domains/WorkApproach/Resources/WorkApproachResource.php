<?php

declare(strict_types=1);

namespace App\Domains\WorkApproach\Resources;

use App\Domains\WorkApproach\Models\WorkApproach;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A "How I work" principle.
 */
#[MapName(SnakeCaseMapper::class)]
final class WorkApproachResource extends Resource
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public ?string $snippet,
        public ?string $icon,
    ) {}

    public static function fromModel(WorkApproach $workApproach): self
    {
        return new self(
            id: $workApproach->id,
            title: $workApproach->title,
            description: $workApproach->description,
            snippet: $workApproach->snippet,
            icon: $workApproach->icon,
        );
    }
}
