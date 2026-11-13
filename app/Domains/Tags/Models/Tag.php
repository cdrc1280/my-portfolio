<?php

declare(strict_types=1);

namespace App\Domains\Tags\Models;

use App\Domains\Blog\Models\Post;
use App\Domains\Projects\Models\Project;
use App\Domains\Tags\Enums\TagCategory;
use App\Domains\Tags\Policies\TagPolicy;
use App\Shared\Concerns\HasPosition;
use App\Shared\Concerns\LogsModelActivity;
use Carbon\CarbonImmutable;
use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * A technology. It is a project/post badge, and with a category it also appears in the
 * public skills section.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property TagCategory|null $category
 * @property string|null $icon
 * @property int $position
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['name', 'slug', 'category', 'icon', 'position'])]
#[UseFactory(TagFactory::class)]
#[UsePolicy(TagPolicy::class)]
class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory, HasPosition;

    use LogsModelActivity;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => TagCategory::class,
            'position' => 'integer',
        ];
    }

    /**
     * Tags shown in the skills section: categorized, in category then manual order.
     *
     * @param  Builder<static>  $query
     */
    public function scopeSkills(Builder $query): void
    {
        $query->whereNotNull('category')->orderBy('position')->orderBy('name');
    }

    /**
     * @return MorphToMany<Project, $this>
     */
    public function projects(): MorphToMany
    {
        return $this->morphedByMany(Project::class, 'taggable');
    }

    /**
     * @return MorphToMany<Post, $this>
     */
    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }
}
