<?php

declare(strict_types=1);

namespace App\Domain\Experience\Models;

use App\Domain\Experience\Enums\EmploymentType;
use App\Domain\Experience\Policies\ExperiencePolicy;
use App\Domain\Shared\Concerns\HasPosition;
use Carbon\CarbonImmutable;
use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $company
 * @property string|null $company_url
 * @property string $role
 * @property string|null $location
 * @property EmploymentType $employment_type
 * @property CarbonImmutable $started_on
 * @property CarbonImmutable|null $ended_on
 * @property string|null $summary
 * @property list<string>|null $highlights
 * @property list<string>|null $technologies
 * @property int $position
 */
#[Fillable([
    'company', 'company_url', 'role', 'location', 'employment_type', 'started_on', 'ended_on',
    'summary', 'highlights', 'technologies', 'position',
])]
#[UseFactory(ExperienceFactory::class)]
#[UsePolicy(ExperiencePolicy::class)]
class Experience extends Model
{
    /** @use HasFactory<ExperienceFactory> */
    use HasFactory, HasPosition;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'employment_type' => EmploymentType::class,
            'started_on' => 'immutable_date',
            'ended_on' => 'immutable_date',
            'highlights' => 'array',
            'technologies' => 'array',
            'position' => 'integer',
        ];
    }

    public function isCurrent(): bool
    {
        return $this->ended_on === null;
    }
}
