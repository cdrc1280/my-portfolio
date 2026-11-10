<?php

declare(strict_types=1);

namespace App\Domain\Skills\Models;

use App\Domain\Shared\Concerns\HasPosition;
use App\Domain\Skills\Policies\SkillPolicy;
use Carbon\CarbonImmutable;
use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $skill_category_id
 * @property string $name
 * @property int $position
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read SkillCategory $category
 */
#[Fillable(['skill_category_id', 'name', 'position'])]
#[UseFactory(SkillFactory::class)]
#[UsePolicy(SkillPolicy::class)]
class Skill extends Model
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory, HasPosition;

    /**
     * @return BelongsTo<SkillCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SkillCategory::class, 'skill_category_id');
    }
}
