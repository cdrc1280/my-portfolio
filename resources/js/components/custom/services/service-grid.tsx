import { ContentIcon } from '@/components/custom/content-icon';
import { Reveal } from '@/components/custom/reveal';
import type { Service } from '@/types/domains/services';

/** Services as bordered cards with an icon, title and description. */
export function ServiceGrid({
    services,
    headingLevel: Heading = 'h3',
}: {
    services: Service[];
    headingLevel?: 'h2' | 'h3';
}) {
    return (
        <ul className="grid gap-4 sm:grid-cols-2">
            {services.map((service, index) => (
                <li key={service.id} className="flex">
                    <Reveal delay={(index % 2) * 0.06} className="flex w-full">
                        <div className="flex w-full gap-4 rounded-lg border border-border bg-card p-5 transition-colors duration-200 hover:border-border-strong">
                            <span className="flex size-9 shrink-0 items-center justify-center rounded-md border border-border bg-background">
                                <ContentIcon icon={service.icon} className="size-[18px] text-muted-foreground" />
                            </span>
                            <div className="space-y-1.5">
                                <Heading className="font-semibold text-foreground">{service.title}</Heading>
                                <p className="text-sm leading-relaxed">{service.description}</p>
                            </div>
                        </div>
                    </Reveal>
                </li>
            ))}
        </ul>
    );
}
