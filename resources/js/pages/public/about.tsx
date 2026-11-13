import { Link, usePage } from '@inertiajs/react';
import { BookOpen, Clock, Download, MapPin } from 'lucide-react';
import { CertificateList } from '@/components/custom/experience/certificate-list';
import { EducationList } from '@/components/custom/experience/education-list';
import { ExperienceTimeline } from '@/components/custom/experience/experience-timeline';
import { LoadingImage } from '@/components/custom/loading-image';
import { PageIntro } from '@/components/custom/page-intro';
import { Prose } from '@/components/custom/prose';
import { Reveal } from '@/components/custom/reveal';
import { containers, measure, panelPadding, Section, surface } from '@/components/custom/section';
import { SectionLabel } from '@/components/custom/section-label';
import { JsonLd, personSchema, Seo } from '@/components/custom/seo';
import { Button } from '@/components/ui/button';
import { track } from '@/lib/analytics';
import { cn } from '@/lib/utils';
import { show as contact } from '@/routes/contact';
import { page as shareCard } from '@/routes/og';
import type { Certificate, Education, Experience } from '@/types/domains/experience';

/** App\Http\Resources\AboutProfileResource */
type AboutProfile = {
    name: string;
    headline: string;
    tagline: string | null;
    short_bio: string | null;
    bio_html: string | null;
    location: string | null;
    timezone: string | null;
    currently_learning: string | null;
    avatar_url: string | null;
    resume_url: string | null;
};

type Props = {
    profile: AboutProfile;
    experiences: Experience[];
    education: Education[];
    certificates: Certificate[];
};

export default function About({ profile, experiences, education, certificates }: Props) {
    const { site, appUrl } = usePage().props;

    return (
        <>
            <Seo
                title="About"
                description={profile.short_bio ?? profile.tagline ?? `About ${profile.name}`}
                image={profile.avatar_url}
                card={shareCard.url('about')}
                type="profile"
            />
            <JsonLd id="about" data={{ '@type': 'ProfilePage', mainEntity: personSchema(site, appUrl) }} />

            <PageIntro
                label="about"
                title={`Hi, I'm ${profile.name.split(' ')[0]}.`}
                description={profile.short_bio ?? profile.tagline}
            >
                <div className="flex flex-wrap items-center gap-3">
                    {profile.resume_url && (
                        <Button asChild>
                            <a href={profile.resume_url} download onClick={() => track('Résumé download')}>
                                <Download aria-hidden /> Download résumé (PDF)
                            </a>
                        </Button>
                    )}
                    <Button asChild variant="outline">
                        <Link href={contact()}>Hire me</Link>
                    </Button>
                    {profile.location && (
                        <span className="inline-flex items-center gap-1.5 font-mono text-xs">
                            <MapPin className="size-4 text-muted-foreground" aria-hidden /> {profile.location}
                        </span>
                    )}
                    {profile.timezone && (
                        <span className="inline-flex items-center gap-1.5 font-mono text-xs">
                            <Clock className="size-4 text-muted-foreground" aria-hidden /> {profile.timezone}
                        </span>
                    )}
                </div>
            </PageIntro>

            {(profile.bio_html || profile.avatar_url) && (
                <div className={cn(containers.page, 'py-3 sm:py-4')}>
                    <Reveal
                        className={cn(surface, panelPadding, 'grid gap-8 sm:grid-cols-[10rem_minmax(0,48rem)] lg:gap-12')}
                    >
                        {profile.avatar_url && (
                            <LoadingImage
                                src={profile.avatar_url}
                                alt={`Portrait of ${profile.name}`}
                                width={160}
                                height={200}
                                loading="eager"
                                frameClassName="aspect-[4/5] w-32 rounded-lg border border-border sm:w-40"
                                className="object-cover"
                            />
                        )}
                        {profile.bio_html && <Prose html={profile.bio_html} className="min-w-0" />}
                    </Reveal>
                </div>
            )}


            {profile.currently_learning && (
                <div className={cn(containers.page, 'py-3 sm:py-4')}>
                    <Reveal className={cn(surface, 'flex gap-3 p-5 sm:p-6')}>
                        <BookOpen className="mt-0.5 size-5 shrink-0 text-primary" aria-hidden />
                        <div className="space-y-1">
                            <SectionLabel>currently learning</SectionLabel>
                            <p className="leading-relaxed text-foreground">{profile.currently_learning}</p>
                        </div>
                    </Reveal>
                </div>
            )}

            {experiences.length > 0 && (
                <Section id="experience" label="experience" title="Experience">
                    <div className={measure}>
                        <ExperienceTimeline experiences={experiences} />
                    </div>
                </Section>
            )}


            {(education.length > 0 || certificates.length > 0) && (
                <Section id="credentials" label="education & certifications" title="Education & certifications" className="pb-20">
                    <div className="grid gap-12 lg:grid-cols-2 lg:gap-16">
                        {education.length > 0 && (
                            <Reveal className="space-y-5">
                                <h3 className="font-mono text-xs text-foreground">education</h3>
                                <EducationList education={education} />
                            </Reveal>
                        )}
                        {certificates.length > 0 && (
                            <Reveal delay={0.06} className="space-y-5">
                                <h3 className="font-mono text-xs text-foreground">certifications</h3>
                                <CertificateList certificates={certificates} />
                            </Reveal>
                        )}
                    </div>
                </Section>
            )}
        </>
    );
}
