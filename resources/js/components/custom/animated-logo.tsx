import { motion, useReducedMotion } from 'motion/react';
import { LOGO_STRATA } from '@/components/custom/app-logo-icon';
import { cn } from '@/lib/utils';

/** The strata mark drawing itself in, one layer at a time. Static with reduced motion. */
export function AnimatedLogo({ className }: { className?: string }) {
    const reduceMotion = useReducedMotion();

    return (
        <svg viewBox="0 0 32 32" aria-hidden className={cn('text-primary', className)}>
            <g fill="none" stroke="currentColor" strokeWidth="2.6" strokeLinecap="round">
                {LOGO_STRATA.map(([y, from, to], index) => (
                    <motion.path
                        key={y}
                        d={`M${from} ${y}H${to}`}
                        initial={reduceMotion ? false : { pathLength: 0, opacity: 0 }}
                        animate={{ pathLength: 1, opacity: 1 }}
                        transition={{ duration: 0.5, delay: 0.15 + index * 0.12, ease: [0.22, 1, 0.36, 1] }}
                    />
                ))}
            </g>
        </svg>
    );
}
