import { Link, usePage } from '@inertiajs/react';
import { ArrowRight, CalendarDays, Clock, Timer } from 'lucide-react';
import { AvailabilityBadge } from '@/components/custom/availability-badge';
import { PageIntro } from '@/components/custom/page-intro';
import { Reveal } from '@/components/custom/reveal';
import { containers, Section } from '@/components/custom/section';
import { Seo } from '@/components/custom/seo';
import { EngagementTypes } from '@/components/custom/services/engagement-types';
import { ProcessSteps } from '@/components/custom/services/process-steps';
import { ServiceGrid } from '@/components/custom/services/service-grid';
import { Button } from '@/components/ui/button';
import { track } from '@/lib/analytics';
import { cn } from '@/lib/utils';
import { show as contact } from '@/routes/contact';
import { page as shareCard } from '@/routes/og';
import type { TitledItem } from '@/types';
import type { Service } from '@/types/domains/services';

type Props = {
    services: Service[];
    engagementTypes: TitledItem[];
    /** Null when no rate is set; the rate line is then hidden. */
    startingRate: string | null;
    processSteps: TitledItem[];
};

export default function Services({ services, engagementTypes, startingRate, processSteps }: Props) {
    const { site } = usePage().props;

    return (
        <>
            <Seo
                title="Services"
                description={`What ${site.name} can build for you, how engagements work and what the process looks like.`}
                card={shareCard.url('services')}
            />

            <PageIntro
                label="services"
                title="How I can help"
                description="Full-stack web development with Laravel, React and TypeScript, available on a part-time contract basis."
            >
                <AvailabilityBadge />
            </PageIntro>

            {services.length > 0 && (
                <Section id="services" label="what i do" title="Services">
                    <ServiceGrid services={services} headingLevel="h3" />
                </Section>
            )}

            {engagementTypes.length > 0 && (
                <Section
                    id="engagement"
                    label="engagement"
                    title="Ways to work together"
                    description="Pick what fits your budget and how predictable the work is."
                >
                    <EngagementTypes types={engagementTypes} startingRate={startingRate} />
                </Section>
            )}

            {processSteps.length > 0 && (
                <Section id="process" label="process" title="From first call to handover">
                    <ProcessSteps steps={processSteps} />
                </Section>
            )}

            {(site.working_hours || site.response_time) && (
                <Section id="logistics" label="logistics" title="Hours and communication">
                    <Reveal>
                        <dl className="grid gap-4 sm:grid-cols-2">
                            {site.working_hours && (
                                <div className="space-y-1 rounded-lg border border-border bg-card p-5">
                                    <dt className="flex items-center gap-2 font-mono text-xs">
                                        <Clock className="size-4 shrink-0 text-muted-foreground" aria-hidden />
                                        working hours
                                    </dt>
                                    <dd className="pl-6 text-foreground">{site.working_hours}</dd>
                                </div>
                            )}
                            {site.response_time && (
                                <div className="space-y-1 rounded-lg border border-border bg-card p-5">
                                    <dt className="flex items-center gap-2 font-mono text-xs">
                                        <Timer className="size-4 shrink-0 text-muted-foreground" aria-hidden />
                                        response time
                                    </dt>
                                    <dd className="pl-6 text-foreground">Replies {site.response_time}</dd>
                                </div>
                            )}
                        </dl>
                    </Reveal>
                </Section>
            )}

            <section aria-labelledby="services-cta-heading" className={cn(containers.page, 'pt-3 pb-24 sm:pt-4')}>
                <Reveal>
                    <div className="flex flex-col gap-6 rounded-2xl border border-border bg-card p-6 sm:p-10 md:flex-row md:items-center md:justify-between">
                        <div className="max-w-xl space-y-2">
                            <h2 id="services-cta-heading" className="text-2xl font-semibold tracking-tight text-foreground">
                                Not sure which option fits?
                            </h2>
                            <p className="leading-relaxed">
                                Send a short brief and I&apos;ll suggest the simplest way to get it done.
                            </p>
                        </div>
                        <div className="flex flex-wrap gap-3">
                            <Button asChild size="lg">
                                <Link href={contact()}>
                                    Send a project brief <ArrowRight aria-hidden />
                                </Link>
                            </Button>
                            {site.booking_url && (
                                <Button asChild size="lg" variant="outline">
                                    <a href={site.booking_url} target="_blank" rel="noreferrer" onClick={() => track('Book a call')}>
                                        <CalendarDays aria-hidden /> Book a call
                                        <span className="sr-only"> (opens in a new tab)</span>
                                    </a>
                                </Button>
                            )}
                        </div>
                    </div>
                </Reveal>
            </section>
        </>
    );
}
