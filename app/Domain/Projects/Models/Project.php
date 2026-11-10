<?php

declare(strict_types=1);

namespace App\Domain\Projects\Models;

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Policies\ProjectPolicy;
use App\Domain\Projects\QueryBuilders\ProjectQueryBuilder;
use App\Domain\Shared\Concerns\HasPosition;
use App\Domain\Tags\Models\Tag;
use Carbon\CarbonImmutable;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $summary
 * @property string|null $body
 * @property string|null $role
 * @property string|null $period
 * @property list<string>|null $highlights
 * @property string|null $cover_image
 * @property string|null $live_url
 * @property string|null $repo_url
 * @property ProjectStatus $status
 * @property bool $is_featured
 * @property CarbonImmutable|null $published_at
 * @property int $position
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, Tag> $tags
 * @property-read Collection<int, ProjectImage> $images
 *
 * @method static ProjectQueryBuilder<static> query()
 */
#[Fillable([
    'title', 'slug', 'summary', 'body', 'role', 'period', 'highlights', 'cover_image',
    'live_url', 'repo_url', 'status', 'is_featured', 'published_at', 'position',
])]
#[UseEloquentBuilder(ProjectQueryBuilder::class)]
#[UseFactory(ProjectFactory::class)]
#[UsePolicy(ProjectPolicy::class)]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, HasPosition;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'draft',
        'is_featured' => false,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'is_featured' => 'boolean',
            'highlights' => 'array',
            'published_at' => 'datetime',
            'position' => 'integer',
        ];
    }

    /**
     * @return MorphToMany<Tag, $this>
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')->orderBy('name');
    }

    /**
     * @return HasMany<ProjectImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('position')->orderBy('id');
    }

    public function isPublished(): bool
    {
        return $this->status === ProjectStatus::Published;
    }
}
