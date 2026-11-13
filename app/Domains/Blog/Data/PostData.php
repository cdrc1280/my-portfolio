<?php

declare(strict_types=1);

namespace App\Domains\Blog\Data;

use App\Domains\Blog\Enums\PostStatus;
use App\Domains\Blog\Models\Post;
use App\Shared\Support\Input;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Throwable;

/**
 * Create/update input for a blog post. The slug falls back to the title; a future publish date
 * on a published post schedules it.
 */
#[MapName(SnakeCaseMapper::class)]
final class PostData extends Data
{
    /**
     * @param  list<int>  $tagIds
     */
    public function __construct(
        public string $title,
        public string $slug,
        public string $excerpt,
        public string $body,
        public PostStatus $status = PostStatus::Draft,
        public ?CarbonImmutable $publishedAt = null,
        public ?string $seoTitle = null,
        public ?string $seoDescription = null,
        public array $tagIds = [],
        public ?UploadedFile $cover = null,
        public bool $removeCover = false,
    ) {}

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        $title = trim((string) ($properties['title'] ?? ''));

        return [
            ...$properties,
            'title' => $title,
            'slug' => Str::slug((string) (Input::nullableString($properties['slug'] ?? null) ?? $title)),
            'excerpt' => trim((string) ($properties['excerpt'] ?? '')),
            'body' => (string) ($properties['body'] ?? ''),
            'published_at' => self::date($properties['published_at'] ?? null),
            'seo_title' => Input::nullableString($properties['seo_title'] ?? null),
            'seo_description' => Input::nullableString($properties['seo_description'] ?? null),
            'tag_ids' => Input::intList($properties['tag_ids'] ?? []),
            'cover' => ($properties['cover'] ?? null) instanceof UploadedFile ? $properties['cover'] : null,
            'remove_cover' => Input::bool($properties['remove_cover'] ?? false),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        $post = request()->route('post');

        return [
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:160', 'alpha_dash', Rule::unique('posts', 'slug')->ignore($post instanceof Post ? $post->id : null)],
            'excerpt' => ['required', 'string', 'max:500'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:200'],
            'body' => ['required', 'string', 'max:100000'],
            'status' => ['required', Rule::enum(PostStatus::class)],
            'published_at' => ['nullable', 'date'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['integer', 'distinct', Rule::exists('tags', 'id')],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_cover' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return ['published_at' => 'publish date', 'tag_ids.*' => 'tag'];
    }

    /**
     * A parsed date, null when empty, or the raw value so the `date` rule can reject it.
     */
    private static function date(mixed $value): mixed
    {
        if ($value instanceof CarbonImmutable || $value === null || $value === '') {
            return $value === '' ? null : $value;
        }

        try {
            return CarbonImmutable::parse((string) $value);
        } catch (Throwable) {
            return $value;
        }
    }
}
