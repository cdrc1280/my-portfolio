<?php

declare(strict_types=1);

namespace App\Domains\Assistant\ValueObjects;

/**
 * One answer from the assistant: plain-text paragraphs, an optional bullet list, links to the
 * relevant pages and follow-up questions to suggest. Never HTML.
 */
final readonly class AssistantReply
{
    /** @var list<string> */
    public array $paragraphs;

    /** @var list<string> */
    public array $bullets;

    /** @var list<array{label: string, href: string}> */
    public array $links;

    /** @var list<string> */
    public array $suggestions;

    /**
     * @param  array<int, string>  $paragraphs
     * @param  array<int, string>  $bullets
     * @param  array<int, array{label: string, href: string}>  $links
     * @param  array<int, string>  $suggestions
     */
    public function __construct(
        public string $intent,
        array $paragraphs,
        array $bullets = [],
        array $links = [],
        array $suggestions = [],
    ) {
        $this->paragraphs = array_values($paragraphs);
        $this->bullets = array_values($bullets);
        $this->links = array_values($links);
        $this->suggestions = array_values($suggestions);
    }

    /**
     * @return array{intent: string, paragraphs: list<string>, bullets: list<string>, links: list<array{label: string, href: string}>, suggestions: list<string>}
     */
    public function toArray(): array
    {
        return [
            'intent' => $this->intent,
            'paragraphs' => $this->paragraphs,
            'bullets' => $this->bullets,
            'links' => $this->links,
            'suggestions' => $this->suggestions,
        ];
    }
}
