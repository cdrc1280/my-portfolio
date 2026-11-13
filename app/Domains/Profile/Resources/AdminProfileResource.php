<?php

declare(strict_types=1);

namespace App\Domains\Profile\Resources;

use App\Domains\Profile\Models\Profile;
use App\Shared\Support\MediaUrl;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * Every editable Settings field, plus the current upload URLs.
 */
#[MapName(SnakeCaseMapper::class)]
final class AdminProfileResource extends Resource
{
    public function __construct(
        public string $name,
        public string $headline,
        public ?string $tagline,
        public ?string $shortBio,
        public ?string $bio,
        public ?string $location,
        public ?string $timezone,
        public ?string $email,
        public ?string $phone,
        public string $availabilityStatus,
        public ?string $availability,
        public ?string $workingHours,
        public ?string $responseTime,
        public ?string $bookingUrl,
        public ?string $currentlyLearning,
        public bool $blogEnabled,
        public bool $assistantEnabled,
        public ?string $seoTitle,
        public ?string $seoDescription,
        public ?string $avatarUrl,
        public ?string $ogImageUrl,
        public ?string $resumeUrl,
        public ?string $resumeUpdatedAt,
    ) {}

    public static function fromModel(Profile $profile): self
    {
        return new self(
            name: $profile->name,
            headline: $profile->headline,
            tagline: $profile->tagline,
            shortBio: $profile->short_bio,
            bio: $profile->bio,
            location: $profile->location,
            timezone: $profile->timezone,
            email: $profile->email,
            phone: $profile->phone,
            availabilityStatus: $profile->availability_status->value,
            availability: $profile->availability,
            workingHours: $profile->working_hours,
            responseTime: $profile->response_time,
            bookingUrl: $profile->booking_url,
            currentlyLearning: $profile->currently_learning,
            blogEnabled: $profile->blog_enabled,
            assistantEnabled: $profile->assistant_enabled,
            seoTitle: $profile->seo_title,
            seoDescription: $profile->seo_description,
            avatarUrl: MediaUrl::for($profile->avatar_path),
            ogImageUrl: MediaUrl::for($profile->og_image_path),
            resumeUrl: $profile->resume_path ? route('resume.download', absolute: false) : null,
            resumeUpdatedAt: $profile->resume_updated_at?->toIso8601String(),
        );
    }
}
