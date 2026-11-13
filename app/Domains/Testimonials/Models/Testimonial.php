<?php

declare(strict_types=1);

namespace App\Domains\Testimonials\Models;

use App\Domains\Projects\Models\Project;
use App\Domains\Testimonials\Policies\TestimonialPolicy;
use App\Shared\Concerns\HasPosition;
use App\Shared\Concerns\LogsModelActivity;
use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A client quote. Only shown publicly when `has_permission` is true.
 *
 * @property int $id
 * @property string $author_name
 * @property string|null $author_role
 * @property string|null $company
 * @property string $quote
 * @property int|null $project_id
 * @property bool $has_permission
 * @property bool $is_featured
 * @property string|null $photo_path
 * @property int $position
 * @property-read Project|null $project
 */
#[Fillable(['author_name', 'author_role', 'company', 'quote', 'project_id', 'has_permission', 'is_featured', 'photo_path', 'position'])]
#[UseFactory(TestimonialFactory::class)]
#[UsePolicy(TestimonialPolicy::class)]
class Testimonial extends Model
{
    /** @use HasFactory<TestimonialFactory> */
    use HasFactory, HasPosition;

    use LogsModelActivity;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'has_permission' => false,
        'is_featured' => false,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'project_id' => 'integer',
            'has_permission' => 'boolean',
            'is_featured' => 'boolean',
            'position' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Only testimonials the client agreed to publish.
     *
     * @param  Builder<static>  $query
     */
    public function scopePublishable(Builder $query): void
    {
        $query->where('has_permission', true);
    }

    /**
     * @param  Builder<static>  $query
     */
    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }
}
