<?php

declare(strict_types=1);

namespace App\Domain\Profile\DataTransferObjects;

use App\Domain\Shared\Support\Input;
use Illuminate\Http\UploadedFile;

/**
 * Input for UpdateProfileAction.
 */
final readonly class ProfileData
{
    public function __construct(
        public string $name,
        public string $headline,
        public ?string $tagline = null,
        public ?string $bio = null,
        public ?string $location = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $availability = null,
        public ?UploadedFile $avatar = null,
        public bool $removeAvatar = false,
        public ?UploadedFile $resume = null,
        public bool $removeResume = false,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated): self
    {
        $file = fn (string $key): ?UploadedFile => ($validated[$key] ?? null) instanceof UploadedFile ? $validated[$key] : null;

        return new self(
            name: trim((string) $validated['name']),
            headline: trim((string) $validated['headline']),
            tagline: Input::nullableString($validated['tagline'] ?? null),
            bio: Input::nullableString($validated['bio'] ?? null),
            location: Input::nullableString($validated['location'] ?? null),
            email: Input::nullableString($validated['email'] ?? null),
            phone: Input::nullableString($validated['phone'] ?? null),
            availability: Input::nullableString($validated['availability'] ?? null),
            avatar: $file('avatar'),
            removeAvatar: (bool) ($validated['remove_avatar'] ?? false),
            resume: $file('resume'),
            removeResume: (bool) ($validated['remove_resume'] ?? false),
        );
    }

    /**
     * @return array<string, string|null>
     */
    public function attributes(): array
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
        ];
    }
}
