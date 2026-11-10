<?php

declare(strict_types=1);

namespace App\Domain\Tags\DataTransferObjects;

use Illuminate\Support\Str;

final readonly class TagData
{
    public function __construct(
        public string $name,
        public string $slug,
    ) {}

    /**
     * @param  array{name: string, slug?: string|null}  $validated
     */
    public static function fromArray(array $validated): self
    {
        $name = trim($validated['name']);

        return new self(
            name: $name,
            slug: Str::slug(($validated['slug'] ?? null) ?: $name),
        );
    }

    /**
     * @return array{name: string, slug: string}
     */
    public function toArray(): array
    {
        return ['name' => $this->name, 'slug' => $this->slug];
    }
}
