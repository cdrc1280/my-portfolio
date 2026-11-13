<?php

declare(strict_types=1);

namespace App\Domains\WorkApproach\Models;

use App\Domains\WorkApproach\Policies\WorkApproachPolicy;
use App\Shared\Concerns\HasPosition;
use App\Shared\Concerns\LogsModelActivity;
use Database\Factories\WorkApproachFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A "How I work" principle, with an optional mono snippet.
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string|null $snippet
 * @property string|null $icon
 * @property int $position
 */
#[Fillable(['title', 'description', 'snippet', 'icon', 'position'])]
#[UseFactory(WorkApproachFactory::class)]
#[UsePolicy(WorkApproachPolicy::class)]
class WorkApproach extends Model
{
    /** @use HasFactory<WorkApproachFactory> */
    use HasFactory, HasPosition;

    use LogsModelActivity;

    protected $table = 'work_approaches';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }
}
