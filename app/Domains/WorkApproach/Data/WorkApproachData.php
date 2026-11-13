<?php

declare(strict_types=1);

namespace App\Domains\WorkApproach\Data;

use App\Shared\Support\Input;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Create/update input for a "How I work" principle.
 */
#[MapName(SnakeCaseMapper::class)]
final class WorkApproachData extends Data
{
    public function __construct(
        public string $title,
        public string $description,
        public ?string $snippet = null,
        public ?string $icon = null,
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
            'title' => trim((string) ($properties['title'] ?? '')),
            'description' => trim((string) ($properties['description'] ?? '')),
            'snippet' => Input::nullableString($properties['snippet'] ?? null),
            'icon' => Input::nullableString($properties['icon'] ?? null),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:600'],
            'snippet' => ['nullable', 'string', 'max:200'],
            'icon' => ['nullable', 'string', 'max:60', 'alpha_dash'],
        ];
    }
}
