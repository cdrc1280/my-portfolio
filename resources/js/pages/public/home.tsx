import { Link, usePage } from '@inertiajs/react';
import { ArrowRight, CalendarDays, Clock, MapPin } from 'lucide-react';
import { AvailabilityBadge } from '@/components/custom/availability-badge';
import { ExperienceTimeline } from '@/components/custom/experience/experience-timeline';
import { LoadingImage } from '@/components/custom/loading-image';
import { ProjectGrid } from '@/components/custom/projects/project-grid';
import { Reveal, RevealWords } from '@/components/custom/reveal';
import { containers, Section } from '@/components/custom/section';
import { SectionLabel } from '@/components/custom/section-label';
import { JsonLd, personSchema, Seo } from '@/components/custom/seo';
import { ServiceGrid } from '@/components/custom/services/service-grid';
import { SkillGroups } from '@/components/custom/tags/skill-groups';
import { TestimonialCard } from '@/components/custom/testimonials/testimonial-card';
import { HowIWork } from '@/components/custom/work-approach/how-i-work';
import { Button } from '@/components/ui/button';
import { track } from '@/lib/analytics';
import { cn } from '@/lib/utils';
import { about, services as servicesPage } from '@/routes';
import { show as contact } from '@/routes/contact';
import { page as shareCard } from '@/routes/og';
import { index as projectsIndex } from '@/routes/projects';
import type { Experience } from '@/types/domains/experience';
import type { ProjectCard } from '@/types/domains/projects';
import type { Service } from '@/types/domains/services';
import type { SkillGroup } from '@/types/domains/tags';
import type { Testimonial } from '@/types/domains/testimonials';
import type { WorkApproach } from '@/types/domains/work-approach';

type Props = {
    featuredProjects: ProjectCard[];
    approaches: WorkApproach[];
    services: Service[];
    skillGroups: SkillGroup[];
    experiences: Experience[];
    testimonials: Testimonial[];
};

export default function Home({ featuredProjects, approaches, services, skillGroups, experiences, testimonials }: Props) {
    const { site, appUrl } = usePage().props;

    return (
        <>
            <Seo type="profile" card={shareCard.url('home')} />
            <JsonLd
                id="site"
                data={{
                    '@graph': [
                        { '@type': 'WebSite', '@id': `${appUrl}/#website`, url: appUrl, name: site.name },
                        personSchema(site, appUrl),
                    ],
                }}
            />

            <Hero hasFeatured={featuredProjects.length > 0} />

            {featuredProjects.length > 0 && (
                <Section
                    id="work"
                    label="featured work"
                    title="Selected case studies"
                    description="A few of the systems I have built and maintained."
                    action={{ label: 'All projects', href: projectsIndex() }}
                >
                    <ProjectGrid projects={featuredProjects} columns={featuredProjects.length === 2 ? 2 : 3} />
                </Section>
            )}

            {approaches.length > 0 && (
                <Section
                    id="how-i-work"
                    label="how i work"
                    title="Habits every project gets"
                    description="So the code is still easy to change a year after I hand it over."
                >
                    <HowIWork approaches={approaches} />
                </Section>
            )}

            {services.length > 0 && (
                <Section
                    id="services"
                    label="services"
                    title="What I can help with"
                    action={{ label: 'Services & process', href: servicesPage() }}
                >
                    <ServiceGrid services={services} />
                </Section>
            )}

            {(skillGroups.length > 0 || experiences.length > 0) && (
                <Section
                    id="stack"
                    label="stack & experience"
                    title="What I work with"
                    action={{ label: 'Full background', href: about() }}
                >
                    <div className="grid gap-12 md:grid-cols-2 md:gap-16">
                        {skillGroups.length > 0 && <SkillGroups groups={skillGroups} />}
                        {experiences.length > 0 && <ExperienceTimeline experiences={experiences} compact maxBullets={2} />}
                    </div>
                </Section>
            )}

            {testimonials.length > 0 && (
                <Section id="testimonials" label="testimonials" title="What clients say">
                    <ul
                        className={cn(
                            'grid gap-4',
                            testimonials.length > 1 && 'md:grid-cols-2',
                            testimonials.length > 2 && 'lg:grid-cols-3',
                        )}
                    >
                        {testimonials.map((testimonial, index) => (
                            <li key={testimonial.id}>
                                <Reveal delay={index * 0.06} className="h-full">
                                    <TestimonialCard testimonial={testimonial} />
                                </Reveal>
                            </li>
                        ))}
                    </ul>
                </Section>
            )}

            <ContactCta />
        </>
    );
}

function Hero({ hasFeatured }: { hasFeatured: boolean }) {
    const { site } = usePage().props;
    const statement = site.tagline ?? `${site.headline}.`;

    return (
        <section aria-labelledby="hero-heading" className={cn(containers.page, 'pt-20 pb-14 sm:pt-28 sm:pb-16')}>
            <div className="grid items-center gap-12 lg:grid-cols-[minmax(0,1fr)_16rem] lg:gap-16">
                <div className="max-w-4xl space-y-6">
                    <Reveal y={6}>
                        <p className="flex flex-wrap items-center gap-x-3 gap-y-1 font-mono text-xs">
                            <span className="text-foreground">{site.name}</span>
                            <span aria-hidden className="text-border-strong">
                                /
                            </span>
                            <span>{site.headline.toLowerCase()}</span>
                        </p>
                    </Reveal>

                    <RevealWords
                        id="hero-heading"
                        text={statement}
                        className="text-4xl leading-[1.1] font-semibold tracking-tight text-foreground md:text-5xl lg:text-6xl"
                    />

                    {site.short_bio && (
                        <Reveal delay={0.2}>
                            <p className="max-w-2xl text-lg leading-relaxed">{site.short_bio}</p>
                        </Reveal>
                    )}

                    <Reveal delay={0.3} className="flex flex-wrap items-center gap-3 pt-2">
                        <Button asChild size="lg">
                            {hasFeatured ? (
                                <a href="#work">
                                    View work <ArrowRight aria-hidden />
                                </a>
                            ) : (
                                <Link href={projectsIndex()}>
                                    View work <ArrowRight aria-hidden />
                                </Link>
                            )}
                        </Button>
                        <Button asChild size="lg" variant="outline">
                            <Link href={contact()}>Hire me</Link>
                        </Button>
                    </Reveal>

                    <Reveal delay={0.4} className="space-y-2">
                        <AvailabilityBadge />
                        {(site.location || site.timezone) && (
                            <p className="flex flex-wrap items-center gap-x-4 gap-y-1 font-mono text-xs">
                                {site.location && (
                                    <span className="inline-flex items-center gap-1.5">
                                        <MapPin className="size-3.5" aria-hidden /> {site.location}
                                    </span>
                                )}
                                {site.timezone && (
                                    <span className="inline-flex items-center gap-1.5">
                                        <Clock className="size-3.5" aria-hidden /> {site.timezone}
                                    </span>
                                )}
                            </p>
                        )}
                    </Reveal>
                </div>

                {site.avatar_url && (
                    <Reveal delay={0.2} className="hidden lg:block">
                        <LoadingImage
                            src={site.avatar_url}
                            alt={`Portrait of ${site.name}`}
                            width={256}
                            height={320}
                            loading="eager"
                            frameClassName="aspect-[4/5] w-full rounded-lg border border-border"
                            className="object-cover"
                        />
                    </Reveal>
                )}
            </div>
        </section>
    );
}

function ContactCta() {
    const { site } = usePage().props;

    return (
        <section aria-labelledby="contact-cta-heading" className={cn(containers.page, 'pt-3 pb-24 sm:pt-4')}>
            <Reveal>
                <div className="flex flex-col gap-8 rounded-2xl border border-border bg-card p-6 sm:p-10 md:flex-row md:items-end md:justify-between">
                    <div className="max-w-xl space-y-3">
                        <SectionLabel>contact</SectionLabel>
                        <h2 id="contact-cta-heading" className="text-2xl font-semibold tracking-tight text-foreground">
                            Have a system that needs building or fixing?
                        </h2>
                        <p className="leading-relaxed">
                            Tell me what you need.
                            {site.response_time ? ` I reply ${site.response_time}.` : ''}
                        </p>
                        <div className="space-y-1.5 pt-1">
                            <AvailabilityBadge />
                            {site.working_hours && (
                                <p className="flex items-center gap-2 font-mono text-xs">
                                    <Clock className="size-3.5 shrink-0" aria-hidden />
                                    {site.working_hours}
                                </p>
                            )}
                        </div>
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
    );
}
