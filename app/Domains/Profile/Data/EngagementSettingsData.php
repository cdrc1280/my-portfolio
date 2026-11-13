<?php

declare(strict_types=1);

namespace App\Domains\Profile\Data;

use App\Shared\Support\Input;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Starting rate, engagement types and process steps shown on the Services page.
 * Rows without a title are dropped before validation.
 */
#[MapName(SnakeCaseMapper::class)]
final class EngagementSettingsData extends Data
{
    /**
     * @param  list<array{title: string, description: string}>  $engagementTypes
     * @param  list<array{title: string, description: string}>  $processSteps
     */
    public function __construct(
        public ?string $startingRate = null,
        public array $engagementTypes = [],
        public array $processSteps = [],
    ) {}

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        return [
            ...$properties,
            'starting_rate' => Input::nullableString($properties['starting_rate'] ?? null),
            'engagement_types' => self::rows($properties['engagement_types'] ?? []),
            'process_steps' => self::rows($properties['process_steps'] ?? []),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'starting_rate' => ['nullable', 'string', 'max:100'],
            'engagement_types' => ['array', 'max:6'],
            'engagement_types.*.title' => ['required', 'string', 'max:100'],
            'engagement_types.*.description' => ['nullable', 'string', 'max:300'],
            'process_steps' => ['array', 'max:8'],
            'process_steps.*.title' => ['required', 'string', 'max:100'],
            'process_steps.*.description' => ['nullable', 'string', 'max:300'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return [
            'engagement_types.*.title' => 'engagement type title',
            'engagement_types.*.description' => 'engagement type description',
            'process_steps.*.title' => 'step title',
            'process_steps.*.description' => 'step description',
        ];
    }

    /**
     * Keeps rows that have a title; trims both fields.
     *
     * @return list<array{title: string, description: string}>
     */
    private static function rows(mixed $rows): array
    {
        $clean = [];

        foreach ((array) $rows as $row) {
            $row = (array) $row;
            $title = trim((string) ($row['title'] ?? ''));

            if ($title !== '') {
                $clean[] = ['title' => $title, 'description' => trim((string) ($row['description'] ?? ''))];
            }
        }

        return $clean;
    }
}
