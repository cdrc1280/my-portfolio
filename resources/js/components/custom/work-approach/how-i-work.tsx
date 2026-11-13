import { ContentIcon } from '@/components/custom/content-icon';
import { Reveal } from '@/components/custom/reveal';
import { cn } from '@/lib/utils';
import type { WorkApproach } from '@/types/domains/work-approach';

/** "How I work" cards from the CMS: icon, title, short explanation and an optional mono snippet. */
export function HowIWork({
    approaches,
    headingLevel: Heading = 'h3',
}: {
    approaches: WorkApproach[];
    headingLevel?: 'h2' | 'h3';
}) {
    return (
        <ul className={cn('grid gap-4 md:grid-cols-2', approaches.length % 3 === 0 && 'lg:grid-cols-3')}>
            {approaches.map((approach, index) => (
                <li key={approach.id} className="flex">
                    <Reveal delay={(index % 3) * 0.06} className="flex w-full">
                        <div className="flex w-full flex-col gap-3 rounded-lg border border-border bg-card p-5 transition-colors duration-200 hover:border-border-strong">
                            <ContentIcon icon={approach.icon} className="size-5 text-muted-foreground" />
                            <Heading className="font-semibold text-foreground">{approach.title}</Heading>
                            <p className="text-sm leading-relaxed">{approach.description}</p>
                            {approach.snippet && (
                                <code className="mt-auto block rounded-md border border-border bg-background px-3 py-2 font-mono text-xs break-words whitespace-pre-wrap text-muted-foreground">
                                    {approach.snippet}
                                </code>
                            )}
                        </div>
                    </Reveal>
                </li>
            ))}
        </ul>
    );
}
