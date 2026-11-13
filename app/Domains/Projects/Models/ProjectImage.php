<?php

declare(strict_types=1);

namespace App\Domains\Projects\Models;

use App\Shared\Concerns\LogsModelActivity;
use Carbon\CarbonImmutable;
use Database\Factories\ProjectImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $project_id
 * @property string $path
 * @property string|null $alt
 * @property int $position
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['path', 'alt', 'position'])]
#[UseFactory(ProjectImageFactory::class)]
class ProjectImage extends Model
{
    /** @use HasFactory<ProjectImageFactory> */
    use HasFactory;

    use LogsModelActivity;

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
