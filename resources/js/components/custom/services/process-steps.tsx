import { Reveal } from '@/components/custom/reveal';
import type { TitledItem } from '@/types';

/** Numbered process (Discovery call → Proposal → Build → Handover), horizontal on wide screens. */
export function ProcessSteps({ steps }: { steps: TitledItem[] }) {
    return (
        <Reveal>
            <ol className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {steps.map((step, index) => (
                    <li key={step.title} className="relative space-y-2 border-t border-border pt-4">
                        <span aria-hidden className="absolute -top-px left-0 h-px w-10 bg-primary" />
                        <p className="font-mono text-xs text-primary">{String(index + 1).padStart(2, '0')}</p>
                        <h3 className="font-semibold text-foreground">{step.title}</h3>
                        {step.description && <p className="text-sm leading-relaxed">{step.description}</p>}
                    </li>
                ))}
            </ol>
        </Reveal>
    );
}
