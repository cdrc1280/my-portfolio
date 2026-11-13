import type { LucideIcon } from 'lucide-react';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

/**
 * Full-width admin page body: the main content fills the space, and on wide screens a sticky
 * side rail (summary, guidance, quick facts) sits on the right. Below xl the rail stacks under it.
 */
export function AdminSplit({
    aside,
    children,
    className,
}: {
    aside: ReactNode;
    children: ReactNode;
    className?: string;
}) {
    return (
        <div
            className={cn(
                'grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem] 2xl:grid-cols-[minmax(0,1fr)_22rem]',
                className,
            )}
        >
            <div className="flex min-w-0 flex-col gap-6">{children}</div>
            <aside className="grid content-start gap-4 xl:sticky xl:top-4 xl:self-start">{aside}</aside>
        </div>
    );
}

/** A card in the side rail. */
export function SidePanel({
    title,
    icon: Icon,
    action,
    className,
    children,
}: {
    title: string;
    icon?: LucideIcon;
    action?: ReactNode;
    className?: string;
    children: ReactNode;
}) {
    return (
        <section aria-label={title} className={cn('rounded-xl border bg-card p-5 text-card-foreground', className)}>
            <header className="mb-3 flex items-center justify-between gap-3">
                <h2 className="flex items-center gap-2 text-sm font-medium">
                    {Icon && <Icon className="size-4 text-muted-foreground" aria-hidden />}
                    {title}
                </h2>
                {action}
            </header>
            {children}
        </section>
    );
}

export type Stat = { label: string; value: ReactNode; hint?: string };

/** Big-number grid for a panel: two columns of label + value. */
export function StatGrid({ stats }: { stats: Stat[] }) {
    return (
        <dl className="grid grid-cols-2 gap-3">
            {stats.map((stat) => (
                <div key={stat.label} className="rounded-lg border bg-background/40 p-3">
                    <dt className="text-xs text-muted-foreground">{stat.label}</dt>
                    <dd className="mt-1 text-xl font-semibold tabular-nums">{stat.value}</dd>
                    {stat.hint && <p className="mt-0.5 text-xs text-muted-foreground">{stat.hint}</p>}
                </div>
            ))}
        </dl>
    );
}

/** Key/value rows for a panel. */
export function FactList({ facts }: { facts: Array<{ term: string; value: ReactNode }> }) {
    return (
        <dl className="divide-y text-sm">
            {facts.map((fact) => (
                <div key={fact.term} className="flex items-baseline justify-between gap-4 py-2 first:pt-0 last:pb-0">
                    <dt className="shrink-0 text-muted-foreground">{fact.term}</dt>
                    <dd className="min-w-0 truncate text-right font-medium">{fact.value}</dd>
                </div>
            ))}
        </dl>
    );
}

/** Horizontal share bars, e.g. items per category. */
export function BreakdownList({
    items,
    empty = 'Nothing yet.',
}: {
    items: Array<{ label: string; count: number }>;
    empty?: string;
}) {
    const max = Math.max(1, ...items.map((item) => item.count));

    if (items.length === 0) {
        return <p className="text-sm text-muted-foreground">{empty}</p>;
    }

    return (
        <ul className="grid gap-2.5 text-sm">
            {items.map((item) => (
                <li key={item.label} className="grid gap-1">
                    <div className="flex items-baseline justify-between gap-3">
                        <span className="truncate">{item.label}</span>
                        <span className="text-xs text-muted-foreground tabular-nums">{item.count}</span>
                    </div>
                    <div className="h-1.5 overflow-hidden rounded-full bg-muted">
                        <div
                            className="h-full rounded-full bg-brand"
                            style={{ width: `${(item.count / max) * 100}%` }}
                        />
                    </div>
                </li>
            ))}
        </ul>
    );
}

/** Short guidance bullets for a panel. */
export function TipList({ tips }: { tips: ReactNode[] }) {
    return (
        <ul className="grid gap-2 text-sm text-muted-foreground">
            {tips.map((tip, index) => (
                <li key={index} className="flex gap-2">
                    <span className="mt-2 size-1 shrink-0 rounded-full bg-muted-foreground/60" aria-hidden />
                    <span>{tip}</span>
                </li>
            ))}
        </ul>
    );
}

/** Groups items by a key and counts them, largest first. */
export function countBy<T>(items: T[], key: (item: T) => string): Array<{ label: string; count: number }> {
    const counts = new Map<string, number>();
    items.forEach((item) => counts.set(key(item), (counts.get(key(item)) ?? 0) + 1));

    return [...counts.entries()].map(([label, count]) => ({ label, count })).sort((a, b) => b.count - a.count);
}
