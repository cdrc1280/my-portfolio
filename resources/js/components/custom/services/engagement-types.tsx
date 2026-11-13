import { Reveal } from '@/components/custom/reveal';
import type { TitledItem } from '@/types';

/** Ways to work together, with an optional "starting at" rate (hidden when empty). */
export function EngagementTypes({ types, startingRate }: { types: TitledItem[]; startingRate: string | null }) {
    return (
        <Reveal className="space-y-6">
            <ul className="grid gap-4 md:grid-cols-3">
                {types.map((type) => (
                    <li key={type.title} className="space-y-2 rounded-lg border border-border bg-card p-5">
                        <h3 className="font-semibold text-foreground">{type.title}</h3>
                        {type.description && <p className="text-sm leading-relaxed">{type.description}</p>}
                    </li>
                ))}
            </ul>
            {startingRate && (
                <p className="font-mono text-xs">
                    <span className="text-foreground">starting at {startingRate}</span>
                    <span aria-hidden className="mx-2 text-border-strong">
                        ·
                    </span>
                    final price depends on scope
                </p>
            )}
        </Reveal>
    );
}
