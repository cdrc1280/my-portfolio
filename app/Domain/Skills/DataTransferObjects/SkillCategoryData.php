<?php

declare(strict_types=1);

namespace App\Domain\Skills\DataTransferObjects;

final readonly class SkillCategoryData
{
    public function __construct(
        public string $name,
    ) {}

    /**
     * @param  array{name: string}  $validated
     */
    public static function fromArray(array $validated): self
    {
        return new self(trim($validated['name']));
    }
}
