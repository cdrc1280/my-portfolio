<?php

declare(strict_types=1);

namespace App\Domains\Projects\Data;

use App\Domains\Projects\Enums\ProjectStatus;
use App\Domains\Projects\Models\Project;
use App\Domains\Projects\Support\FolderTree;
use App\Shared\Support\Input;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Create/update input for a project case study: text sections, metrics, links, dates, status, tags
 * and media (cover, diagram, gallery uploads and removals). The slug falls back to the title.
 */
#[MapName(SnakeCaseMapper::class)]
final class ProjectData extends Data
{
    public const MAX_IMAGE_KB = 4096;

    /**
     * @param  list<array{label: string, value: string}>  $metrics
     * @param  list<int>  $tagIds
     * @param  list<UploadedFile>  $newImages
     * @param  list<int>  $removedImageIds
     */
    public function __construct(
        public string $title,
        public string $slug,
        public string $summary,
        public ?string $problem = null,
        public ?string $role = null,
        public ?string $roleDetails = null,
        public ?string $architecture = null,
        /** The folder tree as typed (indented text); see folderTree(). */
        public ?string $architectureTree = null,
        public ?string $decisions = null,
        public ?string $challenges = null,
        public ?string $results = null,
        public array $metrics = [],
        public ?string $liveUrl = null,
        public ?string $repoUrl = null,
        public ?string $demoUrl = null,
        public bool $isConfidential = false,
        #[WithCast(DateTimeInterfaceCast::class, format: '!Y-m')]
        public ?CarbonImmutable $startedOn = null,
        #[WithCast(DateTimeInterfaceCast::class, format: '!Y-m')]
        public ?CarbonImmutable $endedOn = null,
        public ProjectStatus $status = ProjectStatus::Draft,
        public bool $isFeatured = false,
        public ?string $seoTitle = null,
        public ?string $seoDescription = null,
        public array $tagIds = [],
        public ?UploadedFile $cover = null,
        public bool $removeCover = false,
        public ?UploadedFile $diagram = null,
        public bool $removeDiagram = false,
        #[MapInputName('images')]
        public array $newImages = [],
        public array $removedImageIds = [],
    ) {}

    /**
     * The parsed folder tree, as stored in the `architecture_tree` JSON column.
     *
     * @return list<array<string, mixed>>
     */
    public function folderTree(): array
    {
        return FolderTree::parse($this->architectureTree);
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        $title = trim((string) ($properties['title'] ?? ''));
        $file = fn (string $key): ?UploadedFile => ($properties[$key] ?? null) instanceof UploadedFile ? $properties[$key] : null;

        return [
            ...$properties,
            'title' => $title,
            'slug' => Str::slug((string) (Input::nullableString($properties['slug'] ?? null) ?? $title)),
            'summary' => trim((string) ($properties['summary'] ?? '')),
            'problem' => Input::nullableString($properties['problem'] ?? null),
            'role' => Input::nullableString($properties['role'] ?? null),
            'role_details' => Input::nullableString($properties['role_details'] ?? null),
            'architecture' => Input::nullableString($properties['architecture'] ?? null),
            'architecture_tree' => Input::nullableString($properties['architecture_tree'] ?? null),
            'decisions' => Input::nullableString($properties['decisions'] ?? null),
            'challenges' => Input::nullableString($properties['challenges'] ?? null),
            'results' => Input::nullableString($properties['results'] ?? null),
            'live_url' => Input::nullableString($properties['live_url'] ?? null),
            'repo_url' => Input::nullableString($properties['repo_url'] ?? null),
            'demo_url' => Input::nullableString($properties['demo_url'] ?? null),
            'started_on' => Input::nullableString($properties['started_on'] ?? null),
            'ended_on' => Input::nullableString($properties['ended_on'] ?? null),
            'seo_title' => Input::nullableString($properties['seo_title'] ?? null),
            'seo_description' => Input::nullableString($properties['seo_description'] ?? null),
            'metrics' => self::metrics($properties['metrics'] ?? []),
            'is_confidential' => Input::bool($properties['is_confidential'] ?? false),
            'is_featured' => Input::bool($properties['is_featured'] ?? false),
            'tag_ids' => Input::intList($properties['tag_ids'] ?? []),
            'cover' => $file('cover'),
            'remove_cover' => Input::bool($properties['remove_cover'] ?? false),
            'diagram' => $file('diagram'),
            'remove_diagram' => Input::bool($properties['remove_diagram'] ?? false),
            'images' => array_values(array_filter((array) ($properties['images'] ?? []), fn (mixed $image): bool => $image instanceof UploadedFile)),
            'removed_image_ids' => Input::intList($properties['removed_image_ids'] ?? []),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        $project = request()->route('project');
        $image = ['image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::MAX_IMAGE_KB];

        return [
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:160', 'alpha_dash', Rule::unique('projects', 'slug')->ignore($project instanceof Project ? $project->id : null)],
            'summary' => ['required', 'string', 'max:200'],
            'problem' => ['nullable', 'string', 'max:5000'],
            'role' => ['nullable', 'string', 'max:150'],
            'role_details' => ['nullable', 'string', 'max:5000'],
            'architecture' => ['nullable', 'string', 'max:20000'],
            'architecture_tree' => ['nullable', 'string', 'max:10000'],
            'decisions' => ['nullable', 'string', 'max:10000'],
            'challenges' => ['nullable', 'string', 'max:10000'],
            'results' => ['nullable', 'string', 'max:5000'],
            'metrics' => ['array', 'max:8'],
            'metrics.*.label' => ['nullable', 'string', 'max:60'],
            'metrics.*.value' => ['nullable', 'required_with:metrics.*.label', 'string', 'max:30'],
            'live_url' => ['nullable', 'url:http,https', 'max:255'],
            'repo_url' => ['nullable', 'url:http,https', 'max:255'],
            'demo_url' => ['nullable', 'url:http,https', 'max:255'],
            'is_confidential' => ['boolean'],
            'started_on' => ['nullable', 'date_format:Y-m'],
            'ended_on' => ['nullable', 'date_format:Y-m', 'after_or_equal:started_on'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:200'],
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'is_featured' => ['boolean'],
            'tag_ids' => ['array'],
            'tag_ids.*' => ['integer', 'distinct', Rule::exists('tags', 'id')],
            'cover' => ['nullable', ...$image],
            'remove_cover' => ['boolean'],
            'diagram' => ['nullable', ...$image],
            'remove_diagram' => ['boolean'],
            'images' => ['array', 'max:10'],
            'images.*' => $image,
            'removed_image_ids' => ['array'],
            'removed_image_ids.*' => ['integer'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return [
            'live_url' => 'live URL',
            'repo_url' => 'repository URL',
            'tag_ids' => 'tags',
            'tag_ids.*' => 'tag',
            'images.*' => 'image',
            'demo_url' => 'demo URL',
            'started_on' => 'start date',
            'ended_on' => 'end date',
            'architecture_tree' => 'folder tree',
            'metrics.*.label' => 'metric label',
            'metrics.*.value' => 'metric value',
            'seo_title' => 'SEO title',
            'seo_description' => 'SEO description',
        ];
    }

    /**
     * The metrics to store: rows with both a label and a value.
     *
     * @return list<array{label: string, value: string}>
     */
    public function completeMetrics(): array
    {
        return array_values(array_filter($this->metrics, fn (array $row): bool => $row['label'] !== '' && $row['value'] !== ''));
    }

    /**
     * Trims rows and drops fully empty ones; a label without a value is kept so validation flags it.
     *
     * @return list<array{label: string, value: string}>
     */
    private static function metrics(mixed $rows): array
    {
        $metrics = [];

        foreach ((array) $rows as $row) {
            $row = (array) $row;
            $label = trim((string) ($row['label'] ?? ''));
            $value = trim((string) ($row['value'] ?? ''));

            if ($label !== '' || $value !== '') {
                $metrics[] = ['label' => $label, 'value' => $value];
            }
        }

        return $metrics;
    }
}
