<?php

declare(strict_types=1);

namespace App\Domains\Experience\Resources;

use App\Domains\Experience\Models\Education;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * An education entry (months as `YYYY-MM`; the UI shows years).
 */
#[MapName(SnakeCaseMapper::class)]
final class EducationResource extends Resource
{
    public function __construct(
        public int $id,
        public string $institution,
        public string $qualification,
        public ?string $fieldOfStudy,
        public string $startedOn,
        public ?string $endedOn,
        public ?string $notes,
    ) {}

    public static function fromModel(Education $education): self
    {
        return new self(
            id: $education->id,
            institution: $education->institution,
            qualification: $education->qualification,
            fieldOfStudy: $education->field_of_study,
            startedOn: $education->started_on->format('Y-m'),
            endedOn: $education->ended_on?->format('Y-m'),
            notes: $education->notes,
        );
    }
}
