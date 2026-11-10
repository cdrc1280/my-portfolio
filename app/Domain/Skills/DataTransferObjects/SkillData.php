<?php

declare(strict_types=1);

namespace App\Domain\Skills\DataTransferObjects;

final readonly class SkillData
{
    public function __construct(
        public string $name,
        public int $categoryId,
    ) {}

    /**
     * @param  array{name: string, skill_category_id: int|string}  $validated
     */
    public static function fromArray(array $validated): self
    {
        return new self(trim($validated['name']), (int) $validated['skill_category_id']);
    }

    /**
     * @return array{name: string, skill_category_id: int}
     */
    public function toArray(): array
    {
        return ['name' => $this->name, 'skill_category_id' => $this->categoryId];
    }
}
