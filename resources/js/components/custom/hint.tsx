import type { ReactElement } from 'react';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';

/**
 * Tooltip for a single focusable element (link or button). Use it for icon-only links and for
 * links whose destination isn't obvious; icon `Button`s get one automatically from their aria-label.
 */
export function Hint({
    label,
    side = 'top',
    children,
}: {
    label: string;
    side?: 'top' | 'right' | 'bottom' | 'left';
    children: ReactElement;
}) {
    return (
        <Tooltip>
            <TooltipTrigger asChild>{children}</TooltipTrigger>
            <TooltipContent side={side}>{label}</TooltipContent>
        </Tooltip>
    );
}
