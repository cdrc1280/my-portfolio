import { Link } from '@inertiajs/react';
import type { InertiaLinkProps } from '@inertiajs/react';
import { ArrowRight, ExternalLink, Github, Lock } from 'lucide-react';
import { Hint } from '@/components/custom/hint';
import { TechBadgeList } from '@/components/custom/tech-badge';
import { cn } from '@/lib/utils';

type ProjectCardProps = {
    title: string;
    /** One line; longer text is clamped to two lines. */
    summary: string;
    /** Case study page. */
    href: NonNullable<InertiaLinkProps['href']>;
    tags: string[];
    period?: string | null;
    liveUrl?: string | null;
    repoUrl?: string | null;
    /** Private client work: no repo link, shows a "demo on request" note instead. */
    confidential?: boolean;
    headingLevel?: 'h2' | 'h3';
    className?: string;
};

/** `https://mis.example.com/x` → `mis.example.com`; falls back to the raw string. */
function hostOf(url: string): string {
    try {
        return new URL(url).host;
    } catch {
        return url;
    }
}

const externalLink =
    'relative z-10 inline-flex items-center gap-1 rounded-md text-muted-foreground transition-colors duration-150 hover:text-foreground';

/**
 * Project summary card: title, one-line summary, tech badges and links (case study / live / repo).
 * The whole card opens the case study; the live and repo links sit above that hit area.
 */
export function ProjectCard({
    title,
    summary,
    href,
    tags,
    period,
    liveUrl,
    repoUrl,
    confidential = false,
    headingLevel: Heading = 'h3',
    className,
}: ProjectCardProps) {
    return (
        <article
            className={cn(
                'group relative flex h-full flex-col gap-4 rounded-lg border border-border bg-card p-5 transition-colors duration-200 hover:border-border-strong hover:bg-muted/40 has-[a:focus-visible]:border-primary',
                className,
            )}
        >
            <div className="space-y-2">
                {period && <p className="font-mono text-xs text-muted-foreground">{period}</p>}
                <Heading className="text-lg font-semibold tracking-tight text-foreground">
                    <Link href={href} className="outline-none after:absolute after:inset-0 after:rounded-lg">
                        {title}
                    </Link>
                </Heading>
                <p className="line-clamp-2 text-sm leading-relaxed">{summary}</p>
            </div>

            <TechBadgeList items={tags} />

            <div className="mt-auto flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-border pt-4 text-sm">
                <span className="inline-flex items-center gap-1 font-medium text-primary">
                    Case study
                    <ArrowRight
                        className="size-4 transition-transform duration-150 group-hover:translate-x-0.5"
                        aria-hidden
                    />
                </span>
                {liveUrl && (
                    <Hint label={`Open ${hostOf(liveUrl)}`}>
                        <a href={liveUrl} target="_blank" rel="noreferrer" className={externalLink}>
                            <ExternalLink className="size-4" aria-hidden />
                            Live
                            <span className="sr-only">: {title} (opens in a new tab)</span>
                        </a>
                    </Hint>
                )}
                {confidential && (
                    <span className="inline-flex items-center gap-1 font-mono text-xs text-muted-foreground">
                        <Lock className="size-3.5" aria-hidden />
                        private client
                    </span>
                )}
                {!confidential && repoUrl && (
                    <Hint label={`Source on ${hostOf(repoUrl)}`}>
                        <a href={repoUrl} target="_blank" rel="noreferrer" className={externalLink}>
                            <Github className="size-4" aria-hidden />
                            Code
                            <span className="sr-only">: {title} repository (opens in a new tab)</span>
                        </a>
                    </Hint>
                )}
            </div>
        </article>
    );
}
