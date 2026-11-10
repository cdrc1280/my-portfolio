<?php

declare(strict_types=1);

namespace App\Domain\Skills\Models;

use App\Domain\Shared\Concerns\HasPosition;
use App\Domain\Skills\Policies\SkillPolicy;
use Carbon\CarbonImmutable;
use Database\Factories\SkillCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property int $position
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, Skill> $skills
 */
#[Fillable(['name', 'position'])]
#[UseFactory(SkillCategoryFactory::class)]
#[UsePolicy(SkillPolicy::class)]
class SkillCategory extends Model
{
    /** @use HasFactory<SkillCategoryFactory> */
    use HasFactory, HasPosition;

    /**
     * @return HasMany<Skill, $this>
     */
    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class)->orderBy('position')->orderBy('id');
    }
}
