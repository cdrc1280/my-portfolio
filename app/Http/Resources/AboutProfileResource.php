<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Profile\Models\Profile;
use App\Domain\Shared\Support\Markdown;
use App\Domain\Shared\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Public About-page view of the profile (rendered bio, no phone).
 *
 * @mixin Profile
 */
class AboutProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'headline' => $this->headline,
            'tagline' => $this->tagline,
            'bio_html' => Markdown::toHtml($this->bio),
            'location' => $this->location,
            'availability' => $this->availability,
            'avatar_url' => MediaUrl::for($this->avatar_path),
            'resume_url' => $this->resume_path ? route('resume.download', absolute: false) : null,
        ];
    }
}
