<?php

declare(strict_types=1);

namespace App\Domains\Experience\Models;

use App\Domains\Experience\Enums\EmploymentType;
use App\Domains\Experience\Policies\ExperiencePolicy;
use App\Shared\Concerns\HasPosition;
use App\Shared\Concerns\LogsModelActivity;
use Carbon\CarbonImmutable;
use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $company
 * @property bool $is_confidential
 * @property bool $is_remote
 * @property string|null $company_url
 * @property string $role
 * @property string|null $location
 * @property EmploymentType|null $employment_type
 * @property CarbonImmutable $started_on
 * @property CarbonImmutable|null $ended_on
 * @property string|null $summary
 * @property list<string>|null $highlights
 * @property list<string>|null $technologies
 * @property int $position
 */
#[Fillable([
    'company', 'company_url', 'is_confidential', 'role', 'location', 'is_remote', 'employment_type', 'started_on', 'ended_on',
    'summary', 'highlights', 'technologies', 'position',
])]
#[UseFactory(ExperienceFactory::class)]
#[UsePolicy(ExperiencePolicy::class)]
class Experience extends Model
{
    /** @use HasFactory<ExperienceFactory> */
    use HasFactory, HasPosition;

    use LogsModelActivity;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'employment_type' => EmploymentType::class,
            'is_confidential' => 'boolean',
            'is_remote' => 'boolean',
            'started_on' => 'immutable_date',
            'ended_on' => 'immutable_date',
            'highlights' => 'array',
            'technologies' => 'array',
            'position' => 'integer',
        ];
    }

    /**
     * What the public sees as the employer: hidden for confidential clients.
     */
    public function displayCompany(): string
    {
        return $this->is_confidential || $this->company === null ? 'Confidential client' : $this->company;
    }

    public function isCurrent(): bool
    {
        return $this->ended_on === null;
    }
}
