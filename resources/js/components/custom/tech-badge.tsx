import { cn } from '@/lib/utils';

/** A technology as a small mono tag: thin border, muted text. */
export function TechBadge({ children, className }: { children: string; className?: string }) {
    return (
        <span
            className={cn(
                'inline-flex items-center rounded-md border border-border px-2 py-0.5 font-mono text-xs whitespace-nowrap text-muted-foreground',
                className,
            )}
        >
            {children}
        </span>
    );
}

/** A wrapping list of TechBadges. */
export function TechBadgeList({
    items,
    label = 'Technologies',
    className,
}: {
    items: string[];
    label?: string;
    className?: string;
}) {
    if (items.length === 0) {
        return null;
    }

    return (
        <ul className={cn('flex flex-wrap gap-1.5', className)} aria-label={label}>
            {items.map((item) => (
                <li key={item}>
                    <TechBadge>{item}</TechBadge>
                </li>
            ))}
        </ul>
    );
}
