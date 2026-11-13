import { cn } from '@/lib/utils';

type ProseProps = {
    /**
     * HTML rendered server-side by App\Shared\Support\Markdown, which
     * strips raw HTML and unsafe links. Never pass user-supplied HTML here.
     */
    html: string;
    className?: string;
};

/** Long-form Markdown output in the site theme (follows dark/light via the colour tokens). */
export function Prose({ html, className }: ProseProps) {
    return (
        <div
            className={cn(
                'prose max-w-none prose-zinc dark:prose-invert',
                'prose-p:leading-relaxed prose-p:text-muted-foreground prose-li:text-muted-foreground',
                'prose-headings:font-semibold prose-headings:tracking-tight prose-headings:text-foreground prose-h2:text-2xl',
                'prose-strong:text-foreground',
                'prose-a:text-primary prose-a:underline-offset-4 hover:prose-a:text-foreground',
                'prose-blockquote:border-l-primary prose-blockquote:font-normal prose-blockquote:text-foreground',
                'prose-code:rounded prose-code:border prose-code:border-border prose-code:bg-muted prose-code:px-1.5 prose-code:py-0.5 prose-code:font-mono prose-code:text-[0.875em] prose-code:font-normal prose-code:before:content-none prose-code:after:content-none',
                'prose-pre:rounded-lg prose-pre:border prose-pre:border-border prose-pre:bg-card prose-pre:text-foreground',
                'prose-img:rounded-lg prose-img:border prose-img:border-border prose-hr:border-border',
                className,
            )}
            dangerouslySetInnerHTML={{ __html: html }}
        />
    );
}
