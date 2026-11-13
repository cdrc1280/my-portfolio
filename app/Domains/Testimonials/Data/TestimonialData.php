<?php

declare(strict_types=1);

namespace App\Domains\Testimonials\Data;

use App\Shared\Support\Input;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Create/update input for a client testimonial, including an optional photo upload.
 */
#[MapName(SnakeCaseMapper::class)]
final class TestimonialData extends Data
{
    public function __construct(
        public string $authorName,
        public string $quote,
        public ?string $authorRole = null,
        public ?string $company = null,
        public ?int $projectId = null,
        public bool $hasPermission = false,
        public bool $isFeatured = false,
        public ?UploadedFile $photo = null,
        public bool $removePhoto = false,
    ) {}

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        return [
            ...$properties,
            'author_name' => trim((string) ($properties['author_name'] ?? '')),
            'quote' => trim((string) ($properties['quote'] ?? '')),
            'author_role' => Input::nullableString($properties['author_role'] ?? null),
            'company' => Input::nullableString($properties['company'] ?? null),
            'project_id' => Input::nullableInt($properties['project_id'] ?? null),
            'has_permission' => Input::bool($properties['has_permission'] ?? false),
            'is_featured' => Input::bool($properties['is_featured'] ?? false),
            'photo' => ($properties['photo'] ?? null) instanceof UploadedFile ? $properties['photo'] : null,
            'remove_photo' => Input::bool($properties['remove_photo'] ?? false),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'author_name' => ['required', 'string', 'max:100'],
            'author_role' => ['nullable', 'string', 'max:100'],
            'company' => ['nullable', 'string', 'max:100'],
            'quote' => ['required', 'string', 'max:1500'],
            'project_id' => ['nullable', 'integer', Rule::exists('projects', 'id')],
            'has_permission' => ['boolean'],
            'is_featured' => ['boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'remove_photo' => ['boolean'],
        ];
    }
}
