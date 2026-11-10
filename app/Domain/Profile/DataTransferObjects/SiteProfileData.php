<?php

declare(strict_types=1);

namespace App\Domain\Profile\DataTransferObjects;

use App\Domain\Profile\Models\Profile;
use App\Domain\Profile\Models\SocialLink;
use App\Domain\Shared\Support\MediaUrl;

/**
 * Cached, read-only snapshot of the public profile shared with every page.
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
        public ?string $location,
        public ?string $email,
        public ?string $availability,
        public ?string $avatarUrl,
        public bool $hasResume,
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
            location: $profile->location,
            email: $profile->email,
            availability: $profile->availability,
            avatarUrl: MediaUrl::for($profile->avatar_path),
            hasResume: $profile->resume_path !== null,
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
            'location' => $this->location,
            'email' => $this->email,
            'availability' => $this->availability,
            'avatar_url' => $this->avatarUrl,
            'resume_url' => $this->hasResume ? route('resume.download', absolute: false) : null,
            'socials' => $this->socials,
        ];
    }
}
