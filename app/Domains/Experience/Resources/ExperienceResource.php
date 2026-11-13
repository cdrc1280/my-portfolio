<?php

declare(strict_types=1);

namespace App\Domains\Experience\Resources;

use App\Domains\Experience\Models\Experience;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A work history entry as shown publicly. Confidential clients are never named; see
 * AdminExperienceResource for the raw values.
 */
#[MapName(SnakeCaseMapper::class)]
final class ExperienceResource extends Resource
{
    /**
     * @param  list<string>  $highlights
     * @param  list<string>  $technologies
     */
    public function __construct(
        public int $id,
        public ?string $company,
        public ?string $companyUrl,
        public bool $isConfidential,
        public string $role,
        public ?string $location,
        public bool $isRemote,
        public ?string $employmentType,
        public ?string $employmentTypeLabel,
        public string $startedOn,
        public ?string $endedOn,
        public bool $isCurrent,
        public ?string $summary,
        public array $highlights,
        public array $technologies,
    ) {}

    public static function fromModel(Experience $experience): self
    {
        return new self(
            id: $experience->id,
            company: $experience->displayCompany(),
            companyUrl: $experience->is_confidential ? null : $experience->company_url,
            isConfidential: $experience->is_confidential,
            role: $experience->role,
            location: $experience->location,
            isRemote: $experience->is_remote,
            employmentType: $experience->employment_type?->value,
            employmentTypeLabel: $experience->employment_type?->label(),
            startedOn: $experience->started_on->format('Y-m'),
            endedOn: $experience->ended_on?->format('Y-m'),
            isCurrent: $experience->isCurrent(),
            summary: $experience->summary,
            highlights: $experience->highlights ?? [],
            technologies: $experience->technologies ?? [],
        );
    }
}
