/** App\Domains\Assistant\ValueObjects\AssistantReply */
export type AssistantReply = {
    intent: string;
    paragraphs: string[];
    bullets: string[];
    links: Array<{ label: string; href: string }>;
    suggestions: string[];
};

export type ChatMessage =
    { id: number; from: 'visitor'; text: string } | { id: number; from: 'assistant'; reply: AssistantReply };
