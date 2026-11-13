<?php

declare(strict_types=1);

namespace App\Domains\Uses\Resources;

use App\Domains\Uses\Models\UsesItem;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A /uses entry.
 */
#[MapName(SnakeCaseMapper::class)]
final class UsesItemResource extends Resource
{
    public function __construct(
        public int $id,
        public string $category,
        public string $name,
        public ?string $description,
        public ?string $url,
    ) {}

    public static function fromModel(UsesItem $usesItem): self
    {
        return new self(
            id: $usesItem->id,
            category: $usesItem->category,
            name: $usesItem->name,
            description: $usesItem->description,
            url: $usesItem->url,
        );
    }
}
