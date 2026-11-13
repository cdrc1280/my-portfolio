<?php

declare(strict_types=1);

namespace App\Domains\Seo\ValueObjects;

use DateTimeInterface;
use InvalidArgumentException;

final readonly class SitemapEntry
{
    public function __construct(
        public string $url,
        public ?DateTimeInterface $lastModified = null,
        public float $priority = 0.5,
    ) {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid sitemap URL [{$url}].");
        }

        if ($priority < 0.0 || $priority > 1.0) {
            throw new InvalidArgumentException('Sitemap priority must be between 0.0 and 1.0.');
        }
    }
}
