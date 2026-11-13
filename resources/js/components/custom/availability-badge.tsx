import { usePage } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import type { AvailabilityStatus } from '@/types';

/** Dot colour per status; only "available" uses the accent. */
const dot: Record<AvailabilityStatus, string> = {
    available: 'bg-primary',
    limited: 'bg-amber-500',
    unavailable: 'bg-muted-foreground',
};

/** Mono status line from the site settings, e.g. `● available for new work · open to part-time contracts`. */
export function AvailabilityBadge({ className, withNote = true }: { className?: string; withNote?: boolean }) {
    const { availability } = usePage().props.site;

    return (
        <p className={cn('inline-flex flex-wrap items-center gap-x-2 gap-y-1 font-mono text-xs', className)}>
            <span className={cn('size-2 shrink-0 rounded-full', dot[availability.status])} aria-hidden />
            <span className="text-foreground">{availability.label.toLowerCase()}</span>
            {withNote && availability.note && (
                <>
                    <span aria-hidden className="text-border-strong">
                        ·
                    </span>
                    <span>{availability.note.toLowerCase()}</span>
                </>
            )}
        </p>
    );
}
