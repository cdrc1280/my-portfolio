import { cn } from '@/lib/utils';

/**
 * A progress/share bar made of equal segments, so it needs no inline widths and reads as discrete
 * steps (e.g. 3 of 7 checks). Exposed to assistive tech as an image with a text label.
 */
export function SegmentBar({
    filled,
    segments,
    label,
    className,
}: {
    filled: number;
    segments: number;
    label: string;
    className?: string;
}) {
    return (
        <div role="img" aria-label={label} className={cn('flex gap-1', className)}>
            {Array.from({ length: segments }, (_, index) => (
                <span
                    key={index}
                    className={cn('h-1.5 flex-1 rounded-full', index < filled ? 'bg-primary' : 'bg-muted')}
                />
            ))}
        </div>
    );
}
