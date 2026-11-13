<?php

declare(strict_types=1);

namespace App\Shared\Support;

use Illuminate\Support\Str;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\ExtensionInterface;
use Tempest\Highlight\CommonMark\CodeBlockRenderer;

/**
 * Renders admin-authored Markdown to HTML. Raw HTML is stripped and unsafe
 * links are dropped, so the output is safe to inject on the client. Fenced
 * code blocks are syntax-highlighted on the server (tempest/highlight), so
 * no highlighting JavaScript ships to the browser; token colours come from
 * the `.hl-*` rules in resources/css/app.css.
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
        ], [self::highlighting()]);
    }

    /**
     * Estimated reading time in whole minutes (~200 wpm, minimum 1).
     */
    public static function readingTime(?string $markdown): int
    {
        $words = str_word_count(strip_tags((string) $markdown));

        return max(1, (int) ceil($words / 200));
    }

    /**
     * Highlights fenced code blocks only; inline `code` keeps the default renderer.
     */
    private static function highlighting(): ExtensionInterface
    {
        return new class implements ExtensionInterface
        {
            public function register(EnvironmentBuilderInterface $environment): void
            {
                $environment->addRenderer(FencedCode::class, new CodeBlockRenderer, 10);
            }
        };
    }
}
