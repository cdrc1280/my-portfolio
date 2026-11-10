<?php

declare(strict_types=1);

namespace App\Domain\Experience\DataTransferObjects;

use App\Domain\Experience\Enums\EmploymentType;
use App\Domain\Shared\Support\Input;
use Carbon\CarbonImmutable;

final readonly class ExperienceData
{
    /**
     * @param  list<string>  $highlights
     * @param  list<string>  $technologies
     */
    public function __construct(
        public string $company,
        public string $role,
        public EmploymentType $employmentType,
        public CarbonImmutable $startedOn,
        public ?CarbonImmutable $endedOn = null,
        public ?string $companyUrl = null,
        public ?string $location = null,
        public ?string $summary = null,
        public array $highlights = [],
        public array $technologies = [],
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated): self
    {
        return new self(
            company: trim((string) $validated['company']),
            role: trim((string) $validated['role']),
            employmentType: EmploymentType::from((string) $validated['employment_type']),
            startedOn: Input::month($validated['started_on']) ?? CarbonImmutable::now()->startOfMonth(),
            endedOn: Input::month($validated['ended_on'] ?? null),
            companyUrl: Input::nullableString($validated['company_url'] ?? null),
            location: Input::nullableString($validated['location'] ?? null),
            summary: Input::nullableString($validated['summary'] ?? null),
            highlights: Input::stringList($validated['highlights'] ?? []),
            technologies: Input::stringList($validated['technologies'] ?? []),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'company' => $this->company,
            'company_url' => $this->companyUrl,
            'role' => $this->role,
            'location' => $this->location,
            'employment_type' => $this->employmentType,
            'started_on' => $this->startedOn,
            'ended_on' => $this->endedOn,
            'summary' => $this->summary,
            'highlights' => $this->highlights,
            'technologies' => $this->technologies,
        ];
    }
}
