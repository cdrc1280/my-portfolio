import { Deferred, Link, usePage } from '@inertiajs/react';
import { ArrowRight, Download } from 'lucide-react';
import { Section } from '@/components/shared/section';
import { Seo } from '@/components/shared/seo';
import { Button } from '@/components/ui/button';
import { PostList } from '@/features/blog/components/post-list';
import type { PostCard } from '@/features/blog/types';
import { ExperienceTimeline } from '@/features/experience/components/experience-timeline';
import type { Experience } from '@/features/experience/types';
import { GitHubActivity, GitHubActivitySkeleton } from '@/features/github/components/github-activity';
import type { GitHubActivity as GitHubActivityData } from '@/features/github/types';
import { ProjectGrid } from '@/features/projects/components/project-grid';
import type { ProjectCard } from '@/features/projects/types';
import { SkillGroups } from '@/features/skills/components/skill-groups';
import type { SkillCategory } from '@/features/skills/types';
import { about } from '@/routes';
import { index as blogIndex } from '@/routes/blog';
import { show as contact } from '@/routes/contact';
import { index as projectsIndex } from '@/routes/projects';

type Props = {
    featuredProjects: ProjectCard[];
    skillCategories: SkillCategory[];
    experiences: Experience[];
    latestPosts: PostCard[];
    /** Deferred: undefined until loaded, null when GitHub is unavailable. */
    github?: GitHubActivityData | null;
};

export default function Home({ featuredProjects, skillCategories, experiences, latestPosts, github }: Props) {
    const { site } = usePage().props;

    return (
        <>
            <Seo title={undefined} description={site.tagline ?? `${site.name}, ${site.headline}.`} image={site.avatar_url} type="profile" />

            <div className="space-y-24 sm:space-y-32">
                <section aria-labelledby="hero-heading" className="flex flex-col-reverse gap-10 sm:flex-row sm:items-center sm:justify-between">
                    <div className="max-w-2xl space-y-6">
                        {site.availability && (
                            <p className="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs text-muted-foreground">
                                <span className="size-1.5 rounded-full bg-brand" aria-hidden />
                                {site.availability}
                            </p>
                        )}
                        <div className="space-y-3">
                            <h1 id="hero-heading" className="text-4xl font-semibold tracking-tight text-balance sm:text-5xl">
                                {site.name}
                            </h1>
                            <p className="text-xl text-muted-foreground">{site.headline}</p>
                        </div>
                        {site.tagline && <p className="text-lg leading-relaxed text-pretty">{site.tagline}</p>}
                        <div className="flex flex-wrap gap-3 pt-2">
                            <Button size="lg" asChild>
                                <Link href={projectsIndex()}>
                                    View my work <ArrowRight aria-hidden />
                                </Link>
                            </Button>
                            <Button size="lg" variant="outline" asChild>
                                <Link href={contact()}>Get in touch</Link>
                            </Button>
                            {site.resume_url && (
                                <Button size="lg" variant="ghost" asChild>
                                    <a href={site.resume_url}>
                                        <Download aria-hidden /> Résumé
                                    </a>
                                </Button>
                            )}
                        </div>
                    </div>
                    {site.avatar_url && (
                        <img
                            src={site.avatar_url}
                            alt={`Portrait of ${site.name}`}
                            className="size-32 shrink-0 rounded-full border object-cover sm:size-44"
                        />
                    )}
                </section>

                {featuredProjects.length > 0 && (
                    <Section
                        id="work"
                        title="Selected work"
                        description="A few projects I'm proud of."
                        action={{ label: 'All projects', href: projectsIndex() }}
                    >
                        <ProjectGrid projects={featuredProjects} />
                    </Section>
                )}

                {experiences.length > 0 && (
                    <Section id="experience" title="Experience" action={{ label: 'Full background', href: about() }}>
                        <ExperienceTimeline experiences={experiences} />
                    </Section>
                )}

                {skillCategories.length > 0 && (
                    <Section id="skills" title="Skills & tools">
                        <SkillGroups categories={skillCategories} />
                    </Section>
                )}

                <Deferred data="github" fallback={<GitHubSection loading />}>
                    {github ? <GitHubSection activity={github} /> : <></>}
                </Deferred>

                {latestPosts.length > 0 && (
                    <Section id="writing" title="Writing" action={{ label: 'All posts', href: blogIndex() }}>
                        <PostList posts={latestPosts} />
                    </Section>
                )}

                <section aria-labelledby="cta-heading" className="rounded-2xl border bg-muted/40 px-6 py-12 text-center sm:px-12">
                    <h2 id="cta-heading" className="text-2xl font-semibold tracking-tight">
                        Have a project in mind?
                    </h2>
                    <p className="mx-auto mt-2 max-w-md text-muted-foreground">
                        I'm happy to talk about new roles, freelance work or just compare notes.
                    </p>
                    <Button size="lg" className="mt-6" asChild>
                        <Link href={contact()}>Start a conversation</Link>
                    </Button>
                </section>
            </div>
        </>
    );
}

function GitHubSection({ activity, loading = false }: { activity?: GitHubActivityData; loading?: boolean }) {
    return (
        <Section id="github" title="On GitHub" description="Public activity, refreshed hourly.">
            {loading || !activity ? <GitHubActivitySkeleton /> : <GitHubActivity activity={activity} />}
        </Section>
    );
}
