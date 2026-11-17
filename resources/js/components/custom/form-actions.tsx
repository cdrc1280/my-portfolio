import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

/**
 * A page form's submit row. On phones it is pinned to the bottom of the screen, so Save stays within
 * reach on long forms; give the form `pb-20 md:pb-0` so the last field isn't hidden under it. From 768px
 * up it is an ordinary row where it is placed.
 */
export function FormActions({ className, children }: { className?: string; children: ReactNode }) {
    return (
        <div
            className={cn(
                'flex gap-2',
                'max-md:fixed max-md:inset-x-0 max-md:bottom-0 max-md:z-30 max-md:border-t max-md:bg-background/95 max-md:px-4 max-md:pt-3 max-md:pb-[max(0.75rem,env(safe-area-inset-bottom))] max-md:backdrop-blur',
                className,
            )}
        >
            {children}
        </div>
    );
}
