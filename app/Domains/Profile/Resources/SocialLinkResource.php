<?php

declare(strict_types=1);

namespace App\Domains\Profile\Resources;

use App\Domains\Profile\Models\SocialLink;
use Spatie\LaravelData\Resource;

/**
 * A social link in the Settings editor.
 */
final class SocialLinkResource extends Resource
{
    public function __construct(
        public int $id,
        public string $platform,
        public string $label,
        public string $url,
    ) {}

    public static function fromModel(SocialLink $link): self
    {
        return new self(id: $link->id, platform: $link->platform->value, label: $link->platform->label(), url: $link->url);
    }
}
