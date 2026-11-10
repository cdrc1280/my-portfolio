<?php

declare(strict_types=1);

namespace App\Domain\Blog\DataTransferObjects;

use App\Domain\Blog\Enums\PostStatus;
use App\Domain\Shared\Support\Input;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;

final readonly class PostData
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
        public array $tagIds = [],
        public ?UploadedFile $cover = null,
        public bool $removeCover = false,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated): self
    {
        $publishedAt = Input::nullableString($validated['published_at'] ?? null);

        return new self(
            title: trim((string) $validated['title']),
            slug: (string) $validated['slug'],
            excerpt: trim((string) $validated['excerpt']),
            body: (string) $validated['body'],
            status: PostStatus::from((string) $validated['status']),
            publishedAt: $publishedAt !== null ? CarbonImmutable::parse($publishedAt) : null,
            tagIds: Input::intList($validated['tag_ids'] ?? []),
            cover: ($validated['cover'] ?? null) instanceof UploadedFile ? $validated['cover'] : null,
            removeCover: (bool) ($validated['remove_cover'] ?? false),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'status' => $this->status,
        ];
    }
}
