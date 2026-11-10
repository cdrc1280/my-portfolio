import { Link } from '@inertiajs/react';
import type { InertiaLinkProps } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

type SectionProps = {
    id: string;
    title: string;
    description?: string;
    action?: { label: string; href: NonNullable<InertiaLinkProps['href']> };
    className?: string;
    children: ReactNode;
};

/** A titled content section of a public page. */
export function Section({ id, title, description, action, className, children }: SectionProps) {
    return (
        <section aria-labelledby={`${id}-heading`} className={cn('space-y-8', className)}>
            <div className="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div className="space-y-1">
                    <h2 id={`${id}-heading`} className="text-2xl font-semibold tracking-tight">
                        {title}
                    </h2>
                    {description && <p className="text-muted-foreground">{description}</p>}
                </div>
                {action && (
                    <Link
                        href={action.href}
                        className="inline-flex shrink-0 items-center gap-1 text-sm font-medium text-muted-foreground hover:text-foreground"
                    >
                        {action.label} <ArrowRight className="size-4" aria-hidden />
                    </Link>
                )}
            </div>
            {children}
        </section>
    );
}
