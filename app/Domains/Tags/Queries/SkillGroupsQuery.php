<?php

declare(strict_types=1);

namespace App\Domains\Tags\Queries;

use App\Domains\Tags\Enums\TagCategory;
use App\Domains\Tags\Models\Tag;

/**
 * Categorized tags grouped for the public skills section, in TagCategory order. Empty
 * categories are left out.
 */
final class SkillGroupsQuery
{
    /**
     * @return list<array{category: string, label: string, skills: list<array{id: int, name: string, slug: string, icon: string|null}>}>
     */
    public function execute(): array
    {
        $tags = Tag::query()->skills()->get()->groupBy(fn (Tag $tag): string => (string) $tag->category?->value);
        $groups = [];

        foreach (TagCategory::cases() as $category) {
            $skills = $tags->get($category->value);

            if ($skills === null || $skills->isEmpty()) {
                continue;
            }

            /** @var list<array{id: int, name: string, slug: string, icon: string|null}> $items */
            $items = $skills->map(fn (Tag $tag): array => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
                'icon' => $tag->icon,
            ])->values()->all();

            $groups[] = ['category' => $category->value, 'label' => $category->label(), 'skills' => $items];
        }

        return $groups;
    }
}
