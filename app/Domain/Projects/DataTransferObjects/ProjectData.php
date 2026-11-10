<?php

declare(strict_types=1);

namespace App\Domain\Projects\DataTransferObjects;

use App\Domain\Projects\Enums\ProjectStatus;
use Illuminate\Http\UploadedFile;

/**
 * Everything needed to create or update a project, built from a validated
 * request (or by hand in jobs, commands and tests).
 */
final readonly class ProjectData
{
    /**
     * @param  list<string>  $highlights
     * @param  list<int>  $tagIds
     * @param  list<UploadedFile>  $newImages
     * @param  list<int>  $removedImageIds
     */
    public function __construct(
        public string $title,
        public string $slug,
        public string $summary,
        public ?string $body = null,
        public ?string $role = null,
        public ?string $period = null,
        public array $highlights = [],
        public ?string $liveUrl = null,
        public ?string $repoUrl = null,
        public ProjectStatus $status = ProjectStatus::Draft,
        public bool $isFeatured = false,
        public array $tagIds = [],
        public ?UploadedFile $cover = null,
        public bool $removeCover = false,
        public array $newImages = [],
        public array $removedImageIds = [],
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated): self
    {
        /** @var list<string> $highlights */
        $highlights = array_values(array_filter(
            array_map(fn (mixed $line): string => trim((string) $line), (array) ($validated['highlights'] ?? [])),
            fn (string $line): bool => $line !== '',
        ));

        /** @var list<UploadedFile> $newImages */
        $newImages = array_values((array) ($validated['images'] ?? []));

        return new self(
            title: (string) $validated['title'],
            slug: (string) $validated['slug'],
            summary: (string) $validated['summary'],
            body: self::nullableString($validated['body'] ?? null),
            role: self::nullableString($validated['role'] ?? null),
            period: self::nullableString($validated['period'] ?? null),
            highlights: $highlights,
            liveUrl: self::nullableString($validated['live_url'] ?? null),
            repoUrl: self::nullableString($validated['repo_url'] ?? null),
            status: ProjectStatus::from((string) $validated['status']),
            isFeatured: (bool) ($validated['is_featured'] ?? false),
            tagIds: array_map(intval(...), array_values((array) ($validated['tag_ids'] ?? []))),
            cover: ($validated['cover'] ?? null) instanceof UploadedFile ? $validated['cover'] : null,
            removeCover: (bool) ($validated['remove_cover'] ?? false),
            newImages: $newImages,
            removedImageIds: array_map(intval(...), array_values((array) ($validated['removed_image_ids'] ?? []))),
        );
    }

    /**
     * Column values for the projects table (files and relations excluded).
     *
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'body' => $this->body,
            'role' => $this->role,
            'period' => $this->period,
            'highlights' => $this->highlights,
            'live_url' => $this->liveUrl,
            'repo_url' => $this->repoUrl,
            'status' => $this->status,
            'is_featured' => $this->isFeatured,
        ];
    }

    private static function nullableString(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : null;

        return $value === '' ? null : $value;
    }
}
