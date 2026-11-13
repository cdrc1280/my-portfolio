<?php

declare(strict_types=1);

namespace App\Domains\Services\Data;

use App\Shared\Support\Input;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Create/update input for a service.
 */
#[MapName(SnakeCaseMapper::class)]
final class ServiceData extends Data
{
    public function __construct(
        public string $title,
        public string $description,
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
            'icon' => ['nullable', 'string', 'max:60', 'alpha_dash'],
        ];
    }
}
