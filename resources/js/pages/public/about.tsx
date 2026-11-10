import { Link } from '@inertiajs/react';
import { Download, MapPin } from 'lucide-react';
import { Prose } from '@/components/shared/prose';
import { Section } from '@/components/shared/section';
import { Seo } from '@/components/shared/seo';
import { Button } from '@/components/ui/button';
import { CertificateList } from '@/features/experience/components/certificate-list';
import { EducationList } from '@/features/experience/components/education-list';
import { ExperienceTimeline } from '@/features/experience/components/experience-timeline';
import type { Certificate, Education, Experience } from '@/features/experience/types';
import { SkillGroups } from '@/features/skills/components/skill-groups';
import type { SkillCategory } from '@/features/skills/types';
import { show as contact } from '@/routes/contact';

/** App\Http\Resources\AboutProfileResource */
type AboutProfile = {
    name: string;
    headline: string;
    tagline: string | null;
    bio_html: string | null;
    location: string | null;
    availability: string | null;
    avatar_url: string | null;
    resume_url: string | null;
};

type Props = {
    profile: AboutProfile;
    experiences: Experience[];
    education: Education[];
    certificates: Certificate[];
    skillCategories: SkillCategory[];
};

export default function About({ profile, experiences, education, certificates, skillCategories }: Props) {
    return (
        <>
            <Seo title="About" description={profile.tagline ?? `About ${profile.name}`} image={profile.avatar_url} type="profile" />

            <div className="space-y-20">
                <header className="flex flex-col gap-8 sm:flex-row sm:items-start">
                    {profile.avatar_url && (
                        <img src={profile.avatar_url} alt={`Portrait of ${profile.name}`} className="size-28 rounded-full border object-cover" />
                    )}
                    <div className="space-y-4">
                        <div className="space-y-1">
                            <h1 className="text-3xl font-semibold tracking-tight sm:text-4xl">About me</h1>
                            <p className="text-lg text-muted-foreground">
                                {profile.name} · {profile.headline}
                            </p>
                            {profile.location && (
                                <p className="inline-flex items-center gap-1.5 text-sm text-muted-foreground">
                                    <MapPin className="size-4" aria-hidden /> {profile.location}
                                </p>
                            )}
                        </div>
                        <div className="flex flex-wrap gap-3">
                            {profile.resume_url && (
                                <Button asChild>
                                    <a href={profile.resume_url}>
                                        <Download aria-hidden /> Download résumé (PDF)
                                    </a>
                                </Button>
                            )}
                            <Button variant="outline" asChild>
                                <Link href={contact()}>Contact me</Link>
                            </Button>
                        </div>
                    </div>
                </header>

                {profile.bio_html && <Prose html={profile.bio_html} className="max-w-3xl" />}

                {experiences.length > 0 && (
                    <Section id="experience" title="Experience">
                        <ExperienceTimeline experiences={experiences} />
                    </Section>
                )}

                {skillCategories.length > 0 && (
                    <Section id="skills" title="Skills & tools">
                        <SkillGroups categories={skillCategories} />
                    </Section>
                )}

                {education.length > 0 && (
                    <Section id="education" title="Education">
                        <EducationList education={education} />
                    </Section>
                )}

                {certificates.length > 0 && (
                    <Section id="certificates" title="Certifications">
                        <CertificateList certificates={certificates} />
                    </Section>
                )}
            </div>
        </>
    );
}
