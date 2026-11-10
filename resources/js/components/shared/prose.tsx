import { cn } from '@/lib/utils';

type ProseProps = {
    /**
     * HTML rendered server-side by App\Domain\Shared\Support\Markdown, which
     * strips raw HTML and unsafe links. Never pass user-supplied HTML here.
     */
    html: string;
    className?: string;
};

export function Prose({ html, className }: ProseProps) {
    return (
        <div
            className={cn(
                'prose max-w-none prose-neutral dark:prose-invert prose-headings:tracking-tight prose-a:text-brand prose-pre:bg-muted prose-pre:text-foreground',
                className,
            )}
            dangerouslySetInnerHTML={{ __html: html }}
        />
    );
}
