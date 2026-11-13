<?php

declare(strict_types=1);

namespace App\Domains\Profile\Data;

use App\Domains\Profile\Enums\AvailabilityStatus;
use App\Shared\Support\Input;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * The Settings form: identity, bio, availability, contact, SEO defaults, feature switches and the
 * avatar, social image and résumé uploads. Engagement settings have their own Data
 * (EngagementSettingsData).
 */
#[MapName(SnakeCaseMapper::class)]
final class ProfileData extends Data
{
    public function __construct(
        public string $name,
        public string $headline,
        public ?string $tagline = null,
        public ?string $shortBio = null,
        public ?string $bio = null,
        public ?string $location = null,
        public ?string $timezone = null,
        public ?string $email = null,
        public ?string $phone = null,
        public AvailabilityStatus $availabilityStatus = AvailabilityStatus::Available,
        public ?string $availability = null,
        public ?string $workingHours = null,
        public ?string $responseTime = null,
        public ?string $bookingUrl = null,
        public ?string $currentlyLearning = null,
        public bool $blogEnabled = true,
        public bool $assistantEnabled = true,
        public ?string $seoTitle = null,
        public ?string $seoDescription = null,
        public ?UploadedFile $avatar = null,
        public bool $removeAvatar = false,
        public ?UploadedFile $resume = null,
        public bool $removeResume = false,
        public ?UploadedFile $ogImage = null,
        public bool $removeOgImage = false,
    ) {}

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        $file = fn (string $key): ?UploadedFile => ($properties[$key] ?? null) instanceof UploadedFile ? $properties[$key] : null;

        return [
            ...$properties,
            'name' => trim((string) ($properties['name'] ?? '')),
            'headline' => trim((string) ($properties['headline'] ?? '')),
            'tagline' => Input::nullableString($properties['tagline'] ?? null),
            'short_bio' => Input::nullableString($properties['short_bio'] ?? null),
            'bio' => Input::nullableString($properties['bio'] ?? null),
            'location' => Input::nullableString($properties['location'] ?? null),
            'timezone' => Input::nullableString($properties['timezone'] ?? null),
            'email' => Input::nullableString($properties['email'] ?? null),
            'phone' => Input::nullableString($properties['phone'] ?? null),
            'availability' => Input::nullableString($properties['availability'] ?? null),
            'working_hours' => Input::nullableString($properties['working_hours'] ?? null),
            'response_time' => Input::nullableString($properties['response_time'] ?? null),
            'booking_url' => Input::nullableString($properties['booking_url'] ?? null),
            'currently_learning' => Input::nullableString($properties['currently_learning'] ?? null),
            'seo_title' => Input::nullableString($properties['seo_title'] ?? null),
            'seo_description' => Input::nullableString($properties['seo_description'] ?? null),
            'availability_status' => Input::nullableString($properties['availability_status'] ?? null) ?? AvailabilityStatus::Available->value,
            'blog_enabled' => Input::bool($properties['blog_enabled'] ?? true),
            'assistant_enabled' => Input::bool($properties['assistant_enabled'] ?? true),
            'avatar' => $file('avatar'),
            'remove_avatar' => Input::bool($properties['remove_avatar'] ?? false),
            'resume' => $file('resume'),
            'remove_resume' => Input::bool($properties['remove_resume'] ?? false),
            'og_image' => $file('og_image'),
            'remove_og_image' => Input::bool($properties['remove_og_image'] ?? false),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        $image = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];

        return [
            'name' => ['required', 'string', 'max:100'],
            'headline' => ['required', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:500'],
            'short_bio' => ['nullable', 'string', 'max:500'],
            'bio' => ['nullable', 'string', 'max:20000'],
            'location' => ['nullable', 'string', 'max:150'],
            'timezone' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'availability_status' => ['required', Rule::enum(AvailabilityStatus::class)],
            'availability' => ['nullable', 'string', 'max:150'],
            'working_hours' => ['nullable', 'string', 'max:200'],
            'response_time' => ['nullable', 'string', 'max:100'],
            'booking_url' => ['nullable', 'url:http,https', 'max:255'],
            'currently_learning' => ['nullable', 'string', 'max:500'],
            'blog_enabled' => ['boolean'],
            'assistant_enabled' => ['boolean'],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:200'],
            'avatar' => $image,
            'remove_avatar' => ['boolean'],
            'og_image' => $image,
            'remove_og_image' => ['boolean'],
            'resume' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'remove_resume' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return [
            'headline' => 'role title',
            'tagline' => 'value statement',
            'bio' => 'long bio',
            'availability' => 'availability note',
            'booking_url' => 'booking URL',
            'og_image' => 'social image',
            'seo_title' => 'SEO title',
            'seo_description' => 'SEO description',
        ];
    }
}
