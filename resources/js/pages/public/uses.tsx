import { ExternalLink } from 'lucide-react';
import { PageIntro } from '@/components/custom/page-intro';
import { Reveal } from '@/components/custom/reveal';
import { containers, measure, panelPadding, surface } from '@/components/custom/section';
import { SectionLabel } from '@/components/custom/section-label';
import { Seo } from '@/components/custom/seo';
import { cn } from '@/lib/utils';
import { page as shareCard } from '@/routes/og';
import type { UsesItem } from '@/types/domains/uses';

export default function Uses({ items }: { items: UsesItem[] }) {
    // Keep the CMS order, grouping by first appearance of each category.
    const groups = items.reduce<Array<{ category: string; items: UsesItem[] }>>((acc, item) => {
        const group = acc.find((entry) => entry.category === item.category);

        if (group) {
            group.items.push(item);
        } else {
            acc.push({ category: item.category, items: [item] });
        }

        return acc;
    }, []);

    return (
        <>
            <Seo
                title="Uses"
                description="The editor, tools and services I use to build and ship software."
                card={shareCard.url('uses')}
            />

            <PageIntro
                label="uses"
                title="What I use"
                description="The tools behind the work: editor, terminal, services and the checks every change goes through."
            />

            <div className={cn(containers.page, 'pb-24')}>
                <div className="space-y-6">
                    {groups.map((group) => (
                        <Reveal key={group.category} className={cn(surface, panelPadding, 'space-y-4')}>
                            <SectionLabel>{group.category.toLowerCase()}</SectionLabel>
                            <ul className={cn(measure, 'divide-y divide-border')}>
                                {group.items.map((item) => (
                                    <li key={item.id} className="flex flex-col gap-1 py-4 sm:flex-row sm:gap-6">
                                        <p className="shrink-0 font-medium text-foreground sm:w-48">
                                            {item.url ? (
                                                <a
                                                    href={item.url}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    className="inline-flex items-center gap-1.5 rounded-sm underline-offset-4 transition-colors duration-150 hover:text-primary hover:underline"
                                                >
                                                    {item.name}
                                                    <ExternalLink className="size-3.5" aria-hidden />
                                                    <span className="sr-only"> (opens in a new tab)</span>
                                                </a>
                                            ) : (
                                                item.name
                                            )}
                                        </p>
                                        {item.description && <p className="text-sm leading-relaxed">{item.description}</p>}
                                    </li>
                                ))}
                            </ul>
                        </Reveal>
                    ))}
                </div>
            </div>
        </>
    );
}
