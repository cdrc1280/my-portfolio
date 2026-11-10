<?php

declare(strict_types=1);

namespace App\Domain\Shared\Support;

use Illuminate\Support\Str;

/**
 * Renders admin-authored Markdown to HTML. Raw HTML is stripped and unsafe
 * links are dropped, so the output is safe to inject on the client.
 */
final class Markdown
{
    public static function toHtml(?string $markdown): ?string
    {
        if ($markdown === null || trim($markdown) === '') {
            return null;
        }

        return Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 20,
        ]);
    }

    /**
     * Estimated reading time in whole minutes (~200 wpm, minimum 1).
     */
    public static function readingTime(?string $markdown): int
    {
        $words = str_word_count(strip_tags((string) $markdown));

        return max(1, (int) ceil($words / 200));
    }
}
