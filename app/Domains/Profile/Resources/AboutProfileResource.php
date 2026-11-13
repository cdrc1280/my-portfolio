<?php

declare(strict_types=1);

namespace App\Domains\Profile\Resources;

use App\Domains\Profile\Models\Profile;
use App\Shared\Support\Markdown;
use App\Shared\Support\MediaUrl;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * The profile as shown on the About page, with the bio rendered from Markdown on the server.
 */
#[MapName(SnakeCaseMapper::class)]
final class AboutProfileResource extends Resource
{
    public function __construct(
        public string $name,
        public string $headline,
        public ?string $tagline,
        public ?string $shortBio,
        public ?string $bioHtml,
        public ?string $location,
        public ?string $timezone,
        public ?string $currentlyLearning,
        public ?string $avatarUrl,
        public ?string $resumeUrl,
    ) {}

    public static function fromModel(Profile $profile): self
    {
        return new self(
            name: $profile->name,
            headline: $profile->headline,
            tagline: $profile->tagline,
            shortBio: $profile->short_bio,
            bioHtml: Markdown::toHtml($profile->bio),
            location: $profile->location,
            timezone: $profile->timezone,
            currentlyLearning: $profile->currently_learning,
            avatarUrl: MediaUrl::for($profile->avatar_path),
            resumeUrl: $profile->resume_path ? route('resume.download', absolute: false) : null,
        );
    }
}
