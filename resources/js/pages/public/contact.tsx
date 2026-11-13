import { usePage } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import { CalendarDays, CircleDot, Clock, Mail, MapPin, Timer } from 'lucide-react';
import type { ReactNode } from 'react';
import { ContactForm } from '@/components/custom/contact/contact-form';
import { PageIntro } from '@/components/custom/page-intro';
import { Reveal } from '@/components/custom/reveal';
import { containers, surface } from '@/components/custom/section';
import { Seo } from '@/components/custom/seo';
import { SocialLinks } from '@/components/custom/social-links';
import { Button } from '@/components/ui/button';
import { track } from '@/lib/analytics';
import { cn } from '@/lib/utils';
import { page as shareCard } from '@/routes/og';
import type { SelectOption } from '@/types';

type Props = {
    projectTypes: SelectOption[];
    currencies: SelectOption[];
    budgets: Record<string, SelectOption[]>;
    timelines: SelectOption[];
};

export default function Contact({ projectTypes, currencies, budgets, timelines }: Props) {
    const { site } = usePage().props;

    return (
        <>
            <Seo
                title="Contact"
                description={`Hire ${site.name} for part-time Laravel and React contract work. Send a short brief about your project.`}
                card={shareCard.url('contact')}
            />

            <PageIntro
                label="contact"
                title="Let's talk about your project"
                description={`Send a short brief.${site.response_time ? ` I reply ${site.response_time}.` : ''}`}
            />

            <div className={cn(containers.page, 'grid gap-10 pb-24 lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-16')}>
                <Reveal>
                    <div className="rounded-2xl border border-border bg-card p-5 shadow-sm sm:p-8">
                        <ContactForm projectTypes={projectTypes} currencies={currencies} budgets={budgets} timelines={timelines} />
                    </div>
                </Reveal>

                <Reveal delay={0.1}>
                    <aside aria-label="Contact details" className={cn(surface, 'space-y-6 p-5 sm:p-6')}>
                        <dl className="space-y-5">
                            <Detail icon={CircleDot} term="Status">
                                {site.availability.label}
                                {site.availability.note && ` · ${site.availability.note}`}
                            </Detail>
                            {site.working_hours && (
                                <Detail icon={Clock} term="Working hours">
                                    {site.working_hours}
                                </Detail>
                            )}
                            {site.response_time && (
                                <Detail icon={Timer} term="Response time">
                                    Replies {site.response_time}
                                </Detail>
                            )}
                            {site.email && (
                                <Detail icon={Mail} term="Email">
                                    <a
                                        href={`mailto:${site.email}`}
                                        className="rounded-sm break-all text-primary underline-offset-4 hover:underline"
                                    >
                                        {site.email}
                                    </a>
                                </Detail>
                            )}
                            {site.location && (
                                <Detail icon={MapPin} term="Based in">
                                    {site.location}
                                    {site.timezone && ` (${site.timezone})`}
                                </Detail>
                            )}
                        </dl>
                        {site.booking_url && (
                            <Button asChild variant="outline" className="w-full">
                                <a href={site.booking_url} target="_blank" rel="noreferrer" onClick={() => track('Book a call')}>
                                    <CalendarDays aria-hidden /> Book a call instead
                                    <span className="sr-only"> (opens in a new tab)</span>
                                </a>
                            </Button>
                        )}
                        <SocialLinks className="-ml-2" />
                    </aside>
                </Reveal>
            </div>
        </>
    );
}

function Detail({ icon: Icon, term, children }: { icon: LucideIcon; term: string; children: ReactNode }) {
    return (
        <div className="space-y-1">
            <dt className="flex items-center gap-2 font-mono text-xs">
                <Icon className="size-4 shrink-0 text-muted-foreground" aria-hidden />
                {term.toLowerCase()}
            </dt>
            <dd className="pl-6 text-sm text-foreground">{children}</dd>
        </div>
    );
}
