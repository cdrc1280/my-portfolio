<?php

declare(strict_types=1);

namespace App\Domains\Uses\Data;

use App\Shared\Support\Input;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Create/update input for a /uses entry.
 */
#[MapName(SnakeCaseMapper::class)]
final class UsesItemData extends Data
{
    public function __construct(
        public string $category,
        public string $name,
        public ?string $description = null,
        public ?string $url = null,
    ) {}

    /**
     * Trims text and turns empty optional fields into null, before validation.
     *
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        return [
            ...$properties,
            'category' => trim((string) ($properties['category'] ?? '')),
            'name' => trim((string) ($properties['name'] ?? '')),
            'description' => Input::nullableString($properties['description'] ?? null),
            'url' => Input::nullableString($properties['url'] ?? null),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:60'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'url' => ['nullable', 'url:http,https', 'max:255'],
        ];
    }
}
