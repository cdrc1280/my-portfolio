<?php

declare(strict_types=1);

namespace App\Domains\Profile\ValueObjects;

use App\Domains\Profile\Enums\AvailabilityStatus;
use App\Domains\Profile\Models\Profile;
use App\Domains\Profile\Models\SocialLink;
use App\Shared\Support\MediaUrl;

/**
 * Cached, read-only snapshot of the public profile and site settings shared with every page.
 */
final readonly class SiteProfileData
{
    /**
     * @param  list<array{platform: string, label: string, url: string}>  $socials
     */
    public function __construct(
        public string $name,
        public string $headline,
        public ?string $tagline,
        public ?string $shortBio,
        public ?string $location,
        public ?string $timezone,
        public ?string $email,
        public AvailabilityStatus $availabilityStatus,
        public ?string $availability,
        public ?string $workingHours,
        public ?string $responseTime,
        public ?string $bookingUrl,
        public ?string $avatarUrl,
        public bool $hasResume,
        public bool $blogEnabled,
        public bool $assistantEnabled,
        public ?string $seoTitle,
        public ?string $seoDescription,
        public ?string $ogImageUrl,
        public array $socials,
    ) {}

    /**
     * @param  iterable<SocialLink>  $links
     */
    public static function from(Profile $profile, iterable $links): self
    {
        $socials = [];
        foreach ($links as $link) {
            $socials[] = ['platform' => $link->platform->value, 'label' => $link->platform->label(), 'url' => $link->url];
        }

        return new self(
            name: $profile->name,
            headline: $profile->headline,
            tagline: $profile->tagline,
            shortBio: $profile->short_bio,
            location: $profile->location,
            timezone: $profile->timezone,
            email: $profile->email,
            availabilityStatus: $profile->availability_status,
            availability: $profile->availability,
            workingHours: $profile->working_hours,
            responseTime: $profile->response_time,
            bookingUrl: $profile->booking_url,
            avatarUrl: MediaUrl::for($profile->avatar_path),
            hasResume: $profile->resume_path !== null,
            blogEnabled: $profile->blog_enabled,
            assistantEnabled: $profile->assistant_enabled,
            seoTitle: $profile->seo_title,
            seoDescription: $profile->seo_description,
            ogImageUrl: MediaUrl::for($profile->og_image_path),
            socials: $socials,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'headline' => $this->headline,
            'tagline' => $this->tagline,
            'short_bio' => $this->shortBio,
            'location' => $this->location,
            'timezone' => $this->timezone,
            'email' => $this->email,
            'availability' => [
                'status' => $this->availabilityStatus->value,
                'label' => $this->availabilityStatus->label(),
                'note' => $this->availability,
            ],
            'working_hours' => $this->workingHours,
            'response_time' => $this->responseTime,
            'booking_url' => $this->bookingUrl,
            'avatar_url' => $this->avatarUrl,
            'resume_url' => $this->hasResume ? route('resume.download', absolute: false) : null,
            'blog_enabled' => $this->blogEnabled,
            'assistant_enabled' => $this->assistantEnabled,
            'seo' => [
                'title' => $this->seoTitle,
                'description' => $this->seoDescription,
                'image_url' => $this->ogImageUrl,
            ],
            'socials' => $this->socials,
        ];
    }
}
