import type { SVGAttributes } from 'react';

/**
 * The site mark, derived from "Sedistasis": sedimentary strata (and indented lines of code)
 * settling into an S. Five staggered strokes on a 32 × 32 grid that follow `currentColor`.
 * Keep in sync with public/favicon.svg.
 */
export const LOGO_STRATA: ReadonlyArray<readonly [y: number, from: number, to: number]> = [
    [8, 12, 23],
    [12, 9, 15],
    [16, 12, 20],
    [20, 17, 23],
    [24, 9, 20],
];

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg viewBox="0 0 32 32" aria-hidden {...props}>
            <g fill="none" stroke="currentColor" strokeWidth="2.6" strokeLinecap="round">
                {LOGO_STRATA.map(([y, from, to]) => (
                    <path key={y} d={`M${from} ${y}H${to}`} />
                ))}
            </g>
        </svg>
    );
}
