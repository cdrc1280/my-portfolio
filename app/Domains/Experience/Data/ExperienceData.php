<?php

declare(strict_types=1);

namespace App\Domains\Experience\Data;

use App\Domains\Experience\Enums\EmploymentType;
use App\Shared\Support\Input;
use Carbon\CarbonImmutable;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Create/update input for a work history entry. Months arrive as `YYYY-MM`.
 */
#[MapName(SnakeCaseMapper::class)]
final class ExperienceData extends Data
{
    /**
     * @param  list<string>  $highlights
     * @param  list<string>  $technologies
     */
    public function __construct(
        public ?string $company,
        public string $role,
        public ?EmploymentType $employmentType,
        #[WithCast(DateTimeInterfaceCast::class, format: '!Y-m')]
        public CarbonImmutable $startedOn,
        #[WithCast(DateTimeInterfaceCast::class, format: '!Y-m')]
        public ?CarbonImmutable $endedOn = null,
        public ?string $companyUrl = null,
        public ?string $location = null,
        public bool $isConfidential = false,
        public bool $isRemote = false,
        public ?string $summary = null,
        public array $highlights = [],
        public array $technologies = [],
    ) {}

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        return [
            ...$properties,
            'company' => Input::nullableString($properties['company'] ?? null),
            'role' => trim((string) ($properties['role'] ?? '')),
            'employment_type' => Input::nullableString($properties['employment_type'] ?? null),
            'started_on' => Input::nullableString($properties['started_on'] ?? null),
            'ended_on' => Input::nullableString($properties['ended_on'] ?? null),
            'company_url' => Input::nullableString($properties['company_url'] ?? null),
            'location' => Input::nullableString($properties['location'] ?? null),
            'is_confidential' => Input::bool($properties['is_confidential'] ?? false),
            'is_remote' => Input::bool($properties['is_remote'] ?? false),
            'summary' => Input::nullableString($properties['summary'] ?? null),
            'highlights' => Input::stringList($properties['highlights'] ?? []),
            'technologies' => Input::stringList($properties['technologies'] ?? []),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'company' => ['nullable', 'required_unless:is_confidential,true,1', 'string', 'max:150'],
            'is_confidential' => ['boolean'],
            'is_remote' => ['boolean'],
            'company_url' => ['nullable', 'url:http,https', 'max:255'],
            'role' => ['required', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:150'],
            'employment_type' => ['nullable', Rule::enum(EmploymentType::class)],
            'started_on' => ['required', 'date_format:Y-m'],
            'ended_on' => ['nullable', 'date_format:Y-m', 'after_or_equal:started_on'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'highlights' => ['array', 'max:12'],
            'highlights.*' => ['string', 'max:300'],
            'technologies' => ['array', 'max:20'],
            'technologies.*' => ['string', 'max:50'],
        ];
    }
}
