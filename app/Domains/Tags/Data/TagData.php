<?php

declare(strict_types=1);

namespace App\Domains\Tags\Data;

use App\Domains\Tags\Enums\TagCategory;
use App\Domains\Tags\Models\Tag;
use App\Shared\Support\Input;
use Closure;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

/**
 * Create/update input for a tag. The slug is derived from the name when left empty; a clash with an
 * existing tag is then reported on `name`, since a create form may only have that field.
 */
#[MapName(SnakeCaseMapper::class)]
final class TagData extends Data
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?TagCategory $category = null,
        public ?string $icon = null,
    ) {}

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        $name = trim((string) ($properties['name'] ?? ''));
        $slug = trim((string) ($properties['slug'] ?? ''));

        return [
            ...$properties,
            'name' => $name,
            'slug_derived' => $slug === '',
            'slug' => Str::slug($slug !== '' ? $slug : $name),
            'category' => Input::nullableString($properties['category'] ?? null),
            'icon' => Input::nullableString($properties['icon'] ?? null),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(?ValidationContext $context = null): array
    {
        $derived = (bool) ($context?->payload['slug_derived'] ?? false);
        $ignoreId = self::routeTag()?->id;

        $unique = function (string $attribute, mixed $value, Closure $fail) use ($context, $ignoreId): void {
            $slug = (string) ($context?->payload['slug'] ?? '');

            if ($slug !== '' && Tag::query()->where('slug', $slug)->when($ignoreId, fn ($query, int $id) => $query->whereKeyNot($id))->exists()) {
                $fail('A tag with this name already exists.');
            }
        };

        return [
            'name' => ['required', 'string', 'max:50', ...($derived ? [$unique] : [])],
            'slug' => ['required', 'string', 'max:60', 'alpha_dash', ...($derived ? [] : [$unique])],
            'category' => ['nullable', Rule::enum(TagCategory::class)],
            'icon' => ['nullable', 'string', 'max:60', 'alpha_dash'],
        ];
    }

    private static function routeTag(): ?Tag
    {
        $tag = request()->route('tag');

        return $tag instanceof Tag ? $tag : null;
    }
}
