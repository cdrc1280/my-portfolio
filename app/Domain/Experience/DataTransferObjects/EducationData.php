<?php

declare(strict_types=1);

namespace App\Domain\Experience\DataTransferObjects;

use App\Domain\Shared\Support\Input;
use Carbon\CarbonImmutable;

final readonly class EducationData
{
    public function __construct(
        public string $institution,
        public string $qualification,
        public CarbonImmutable $startedOn,
        public ?CarbonImmutable $endedOn = null,
        public ?string $fieldOfStudy = null,
        public ?string $notes = null,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated): self
    {
        return new self(
            institution: trim((string) $validated['institution']),
            qualification: trim((string) $validated['qualification']),
            startedOn: Input::month($validated['started_on']) ?? CarbonImmutable::now()->startOfMonth(),
            endedOn: Input::month($validated['ended_on'] ?? null),
            fieldOfStudy: Input::nullableString($validated['field_of_study'] ?? null),
            notes: Input::nullableString($validated['notes'] ?? null),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'institution' => $this->institution,
            'qualification' => $this->qualification,
            'field_of_study' => $this->fieldOfStudy,
            'started_on' => $this->startedOn,
            'ended_on' => $this->endedOn,
            'notes' => $this->notes,
        ];
    }
}
