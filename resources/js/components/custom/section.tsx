import { Link } from '@inertiajs/react';
import type { InertiaLinkProps } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import type { ReactNode } from 'react';
import { Reveal } from '@/components/custom/reveal';
import { SectionLabel } from '@/components/custom/section-label';
import { cn } from '@/lib/utils';

export const containers = {
    /**
     * The one layout width: header, sections and footer all share this left edge. Keep text readable
     * inside it with `measure` (or max-w-2xl on a paragraph), not by narrowing the container.
     */
    page: 'mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8',
    /** Centered reading column, for long-form article pages (blog posts) only. */
    article: 'mx-auto w-full max-w-3xl px-4 sm:px-6',
} as const;

/** Comfortable line length (~70 characters) for running text and text-heavy lists. */
export const measure = 'max-w-3xl';

/**
 * A content panel over the dot field: near-opaque page colour, so text never sits on the dots while
 * `bg-card` cards inside it still stand out. No blur on phones (it is costly there and invisible at 95%). Add padding where it is used (usually `panelPadding`).
 */
export const surface =
    'rounded-2xl border border-border bg-background/95 shadow-sm sm:bg-background/90 sm:backdrop-blur-md';
export const panelPadding = 'p-6 sm:p-8 lg:p-10';

type SectionProps = {
    id: string;
    label: string;
    title: string;
    description?: ReactNode;
    action?: { label: string; href: NonNullable<InertiaLinkProps['href']> };
    width?: keyof typeof containers;
    className?: string;
    children: ReactNode;
};

/** A page section on its own panel: comment-style label, h2 title, optional description and link, then content. */
export function Section({ id, label, title, description, action, width = 'page', className, children }: SectionProps) {
    return (
        <section id={id} aria-labelledby={`${id}-heading`} className={cn('scroll-mt-16', className)}>
            <div className={cn(containers[width], 'py-3 sm:py-4')}>
                <div className={cn(surface, panelPadding)}>
                    <Reveal className="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                        <div className="max-w-2xl space-y-3">
                            <SectionLabel>{label}</SectionLabel>
                            <h2 id={`${id}-heading`} className="text-2xl font-semibold tracking-tight text-foreground">
                                {title}
                            </h2>
                            {description && <p className="leading-relaxed">{description}</p>}
                        </div>
                        {action && (
                            <Link
                                href={action.href}
                                className="group inline-flex shrink-0 items-center gap-1 rounded-md text-sm font-medium text-primary transition-colors duration-150 hover:text-foreground"
                            >
                                {action.label}
                                <ArrowRight
                                    className="size-4 transition-transform duration-150 group-hover:translate-x-0.5"
                                    aria-hidden
                                />
                            </Link>
                        )}
                    </Reveal>
                    {children}
                </div>
            </div>
        </section>
    );
}
