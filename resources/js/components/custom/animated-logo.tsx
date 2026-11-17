import type { CSSProperties } from 'react';
import { LOGO_STRATA } from '@/components/custom/app-logo-icon';
import { cn } from '@/lib/utils';

/** The strata mark drawing itself in, one layer at a time. Static with reduced motion. */
export function AnimatedLogo({ className }: { className?: string }) {
    return (
        <svg viewBox="0 0 32 32" aria-hidden className={cn('text-primary', className)}>
            <g fill="none" stroke="currentColor" strokeWidth="2.6" strokeLinecap="round">
                {LOGO_STRATA.map(([y, from, to], index) => (
                    <path
                        key={y}
                        d={`M${from} ${y}H${to}`}
                        pathLength={1}
                        className="draw-in"
                        style={{ '--draw-delay': `${0.15 + index * 0.12}s` } as CSSProperties}
                    />
                ))}
            </g>
        </svg>
    );
}
