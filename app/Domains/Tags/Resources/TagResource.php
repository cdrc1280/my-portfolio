<?php

declare(strict_types=1);

namespace App\Domains\Tags\Resources;

use App\Domains\Tags\Models\Tag;
use Spatie\LaravelData\Resource;

/**
 * A tag as a badge or filter option on public and admin pages.
 */
final class TagResource extends Resource
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
    ) {}

    public static function fromModel(Tag $tag): self
    {
        return new self(id: $tag->id, name: $tag->name, slug: $tag->slug);
    }
}
