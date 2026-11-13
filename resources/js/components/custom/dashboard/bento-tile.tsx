import type { LucideIcon } from 'lucide-react';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

type BentoTileProps = {
    title: string;
    icon?: LucideIcon;
    /** Right side of the header (a link or small control). */
    action?: ReactNode;
    className?: string;
    children: ReactNode;
};

/** A dashboard tile: bordered card with a compact header. Size it with grid span classes. */
export function BentoTile({ title, icon: Icon, action, className, children }: BentoTileProps) {
    return (
        <section
            aria-label={title}
            className={cn('flex flex-col rounded-xl border bg-card p-5 text-card-foreground', className)}
        >
            <header className="mb-4 flex items-center justify-between gap-3">
                <h2 className="flex items-center gap-2 text-sm font-medium">
                    {Icon && <Icon className="size-4 text-muted-foreground" aria-hidden />}
                    {title}
                </h2>
                {action}
            </header>
            <div className="flex-1">{children}</div>
        </section>
    );
}
