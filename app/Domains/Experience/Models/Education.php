<?php

declare(strict_types=1);

namespace App\Domains\Experience\Models;

use App\Domains\Experience\Policies\ExperiencePolicy;
use App\Shared\Concerns\HasPosition;
use App\Shared\Concerns\LogsModelActivity;
use Carbon\CarbonImmutable;
use Database\Factories\EducationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $institution
 * @property string $qualification
 * @property string|null $field_of_study
 * @property CarbonImmutable $started_on
 * @property CarbonImmutable|null $ended_on
 * @property string|null $notes
 * @property int $position
 */
#[Table('education')]
#[Fillable(['institution', 'qualification', 'field_of_study', 'started_on', 'ended_on', 'notes', 'position'])]
#[UseFactory(EducationFactory::class)]
#[UsePolicy(ExperiencePolicy::class)]
class Education extends Model
{
    /** @use HasFactory<EducationFactory> */
    use HasFactory, HasPosition;

    use LogsModelActivity;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_on' => 'immutable_date',
            'ended_on' => 'immutable_date',
            'position' => 'integer',
        ];
    }
}
