<?php

declare(strict_types=1);

namespace App\Http\Resources\Admin;

use App\Domain\Profile\Models\Profile;
use App\Domain\Shared\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Profile
 */
class ProfileResource extends JsonResource
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
            'bio' => $this->bio,
            'location' => $this->location,
            'email' => $this->email,
            'phone' => $this->phone,
            'availability' => $this->availability,
            'avatar_url' => MediaUrl::for($this->avatar_path),
            'resume_url' => $this->resume_path ? route('resume.download', absolute: false) : null,
            'resume_updated_at' => $this->resume_updated_at?->toIso8601String(),
        ];
    }
}
