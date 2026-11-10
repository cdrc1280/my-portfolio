<?php

declare(strict_types=1);

namespace App\Domain\Profile\DataTransferObjects;

use App\Domain\Profile\Enums\SocialPlatform;

final readonly class SocialLinkData
{
    public function __construct(
        public SocialPlatform $platform,
        public string $url,
    ) {}

    /**
     * @param  array{platform: string, url: string}  $validated
     */
    public static function fromArray(array $validated): self
    {
        $platform = SocialPlatform::from($validated['platform']);
        $url = trim($validated['url']);

        // Allow a bare address for the Email platform.
        if ($platform === SocialPlatform::Email && ! str_starts_with($url, 'mailto:')) {
            $url = "mailto:{$url}";
        }

        return new self($platform, $url);
    }

    /**
     * @return array{platform: SocialPlatform, url: string}
     */
    public function toArray(): array
    {
        return ['platform' => $this->platform, 'url' => $this->url];
    }
}
