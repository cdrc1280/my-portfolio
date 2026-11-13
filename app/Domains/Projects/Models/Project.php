<?php

declare(strict_types=1);

namespace App\Domains\Projects\Models;

use App\Domains\Projects\Enums\ProjectStatus;
use App\Domains\Projects\Policies\ProjectPolicy;
use App\Domains\Projects\QueryBuilders\ProjectQueryBuilder;
use App\Domains\Tags\Models\Tag;
use App\Domains\Testimonials\Models\Testimonial;
use App\Shared\Concerns\HasPosition;
use App\Shared\Concerns\LogsModelActivity;
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
 * @property string|null $problem
 * @property string|null $role
 * @property string|null $role_details
 * @property string|null $architecture
 * @property list<array<string, mixed>>|null $architecture_tree
 * @property string|null $diagram_path
 * @property string|null $decisions
 * @property string|null $challenges
 * @property string|null $results
 * @property list<array{label: string, value: string}>|null $metrics
 * @property string|null $cover_image
 * @property string|null $live_url
 * @property string|null $repo_url
 * @property string|null $demo_url
 * @property bool $is_confidential
 * @property CarbonImmutable|null $started_on
 * @property CarbonImmutable|null $ended_on
 * @property ProjectStatus $status
 * @property bool $is_featured
 * @property CarbonImmutable|null $published_at
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property int $position
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, Tag> $tags
 * @property-read Collection<int, ProjectImage> $images
 *
 * @method static ProjectQueryBuilder<static> query()
 */
#[Fillable([
    'title', 'slug', 'summary', 'problem', 'role', 'role_details', 'architecture', 'architecture_tree',
    'diagram_path', 'decisions', 'challenges', 'results', 'metrics', 'cover_image', 'live_url', 'repo_url',
    'demo_url', 'is_confidential', 'started_on', 'ended_on', 'status', 'is_featured', 'published_at',
    'seo_title', 'seo_description', 'position',
])]
#[UseEloquentBuilder(ProjectQueryBuilder::class)]
#[UseFactory(ProjectFactory::class)]
#[UsePolicy(ProjectPolicy::class)]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, HasPosition;

    use LogsModelActivity;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'draft',
        'is_featured' => false,
        'is_confidential' => false,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'is_featured' => 'boolean',
            'is_confidential' => 'boolean',
            'architecture_tree' => 'array',
            'metrics' => 'array',
            'started_on' => 'immutable_date',
            'ended_on' => 'immutable_date',
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

    /**
     * @return HasMany<Testimonial, $this>
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    /**
     * Confidential client work never exposes its repository.
     */
    public function publicRepoUrl(): ?string
    {
        return $this->is_confidential ? null : $this->repo_url;
    }

    public function isPublished(): bool
    {
        return $this->status === ProjectStatus::Published;
    }
}
