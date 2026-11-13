import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

type TimelineItemProps = {
    role: string;
    company: string;
    companyUrl?: string | null;
    /** Display text, e.g. "Jul 2023 – Present". */
    dates: string;
    /** Machine-readable start (YYYY-MM). */
    dateTime?: string;
    meta?: string | null;
    bullets?: string[];
    current?: boolean;
    children?: ReactNode;
};

/** One entry of a career timeline: role, company, mono dates and short bullets. Render inside an <ol>. */
export function TimelineItem({
    role,
    company,
    companyUrl,
    dates,
    dateTime,
    meta,
    bullets = [],
    current = false,
    children,
}: TimelineItemProps) {
    return (
        <li className="relative border-l border-border pb-10 pl-6 last:pb-0">
            <span
                aria-hidden
                className={cn(
                    'absolute top-1.5 -left-[5px] size-[9px] rounded-full border border-background',
                    current ? 'bg-primary' : 'bg-border-strong',
                )}
            />
            <p className="font-mono text-xs text-muted-foreground">
                <time dateTime={dateTime}>{dates}</time>
                {current && <span className="ml-2 text-primary">current</span>}
            </p>
            <h3 className="mt-1 font-semibold text-foreground">
                {role}
                <span className="font-normal text-muted-foreground">
                    {' · '}
                    {companyUrl ? (
                        <a
                            href={companyUrl}
                            target="_blank"
                            rel="noreferrer"
                            className="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-foreground hover:underline"
                        >
                            {company}
                        </a>
                    ) : (
                        company
                    )}
                </span>
            </h3>
            {meta && <p className="mt-0.5 text-sm">{meta}</p>}
            {bullets.length > 0 && (
                <ul className="mt-3 space-y-1.5 text-sm leading-relaxed">
                    {bullets.map((bullet) => (
                        <li key={bullet} className="flex gap-2.5">
                            <span aria-hidden className="mt-[0.7em] h-px w-2.5 shrink-0 bg-border-strong" />
                            {bullet}
                        </li>
                    ))}
                </ul>
            )}
            {children}
        </li>
    );
}
