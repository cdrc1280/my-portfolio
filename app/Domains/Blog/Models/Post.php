<?php

declare(strict_types=1);

namespace App\Domains\Blog\Models;

use App\Domains\Blog\Enums\PostStatus;
use App\Domains\Blog\Policies\PostPolicy;
use App\Domains\Blog\QueryBuilders\PostQueryBuilder;
use App\Domains\Tags\Models\Tag;
use App\Shared\Concerns\LogsModelActivity;
use Carbon\CarbonImmutable;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $excerpt
 * @property string $body
 * @property string|null $cover_image
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property PostStatus $status
 * @property CarbonImmutable|null $published_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, Tag> $tags
 *
 * @method static PostQueryBuilder<static> query()
 */
#[Fillable(['title', 'slug', 'excerpt', 'body', 'cover_image', 'status', 'published_at', 'seo_title', 'seo_description'])]
#[UseEloquentBuilder(PostQueryBuilder::class)]
#[UseFactory(PostFactory::class)]
#[UsePolicy(PostPolicy::class)]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    use LogsModelActivity;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'draft',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return MorphToMany<Tag, $this>
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')->orderBy('name');
    }
}
