<?php

declare(strict_types=1);

namespace App\Domains\Experience\Data;

use App\Shared\Support\Input;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Create/update input for an education entry. Months arrive as `YYYY-MM`.
 */
#[MapName(SnakeCaseMapper::class)]
final class EducationData extends Data
{
    public function __construct(
        public string $institution,
        public string $qualification,
        #[WithCast(DateTimeInterfaceCast::class, format: '!Y-m')]
        public CarbonImmutable $startedOn,
        #[WithCast(DateTimeInterfaceCast::class, format: '!Y-m')]
        public ?CarbonImmutable $endedOn = null,
        public ?string $fieldOfStudy = null,
        public ?string $notes = null,
    ) {}

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        return [
            ...$properties,
            'institution' => trim((string) ($properties['institution'] ?? '')),
            'qualification' => trim((string) ($properties['qualification'] ?? '')),
            'started_on' => Input::nullableString($properties['started_on'] ?? null),
            'ended_on' => Input::nullableString($properties['ended_on'] ?? null),
            'field_of_study' => Input::nullableString($properties['field_of_study'] ?? null),
            'notes' => Input::nullableString($properties['notes'] ?? null),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'institution' => ['required', 'string', 'max:150'],
            'qualification' => ['required', 'string', 'max:150'],
            'field_of_study' => ['nullable', 'string', 'max:150'],
            'started_on' => ['required', 'date_format:Y-m'],
            'ended_on' => ['nullable', 'date_format:Y-m', 'after_or_equal:started_on'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
