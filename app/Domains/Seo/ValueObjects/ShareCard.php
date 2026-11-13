<?php

declare(strict_types=1);

namespace App\Domains\Seo\ValueObjects;

/**
 * The text on a generated Open Graph image.
 */
final readonly class ShareCard
{
    public function __construct(
        public string $label,
        public string $title,
        public ?string $subtitle,
        public string $name,
        public ?string $domain,
    ) {}
}
