import { Link, usePage } from '@inertiajs/react';
import { ArrowRight, ExternalLink, Github, Lock, PlayCircle } from 'lucide-react';
import type { ReactNode } from 'react';
import { FolderTree } from '@/components/custom/folder-tree';
import { LoadingImage } from '@/components/custom/loading-image';
import { PageIntro } from '@/components/custom/page-intro';
import { Prose } from '@/components/custom/prose';
import { Reveal } from '@/components/custom/reveal';
import { containers, measure, panelPadding, surface } from '@/components/custom/section';
import { SectionLabel } from '@/components/custom/section-label';
import { JsonLd, Seo } from '@/components/custom/seo';
import { TagList } from '@/components/custom/tag-list';
import { TestimonialCard } from '@/components/custom/testimonials/testimonial-card';
import { Button } from '@/components/ui/button';
import { track } from '@/lib/analytics';
import { formatMonthRange } from '@/lib/format';
import { cn } from '@/lib/utils';
import { show as contact } from '@/routes/contact';
import { project as shareCard } from '@/routes/og';
import { index, show } from '@/routes/projects';
import type { NextProject, Project } from '@/types/domains/projects';
import type { Testimonial } from '@/types/domains/testimonials';
import { ProjectGallery } from './project-gallery';
import { ProjectMetrics } from './project-metrics';

type Props = {
    project: Project;
    testimonial: Testimonial | null;
    next: NextProject | null;
};

/**
 * Case-study template: header → Problem → My role → Architecture → Key decisions → Challenges →
 * Results + metrics → Gallery / demo → Related testimonial → Next project. Empty sections are skipped.
 */
export default function ProjectShow({ project, testimonial, next }: Props) {
    const { site, appUrl } = usePage().props;
    const screenshots = project.cover_url
        ? [{ id: 0, url: project.cover_url, alt: `${project.title}: main screen` }, ...project.images]
        : project.images;
    const hasArchitecture =
        project.architecture_html !== null || project.architecture_tree.length > 0 || project.diagram_url !== null;
    const sections = [
        { id: 'problem', label: 'problem', show: Boolean(project.problem_html) },
        { id: 'role', label: 'my role', show: Boolean(project.role_html) },
        { id: 'architecture', label: 'architecture', show: hasArchitecture },
        { id: 'decisions', label: 'decisions', show: Boolean(project.decisions_html) },
        { id: 'challenges', label: 'challenges', show: Boolean(project.challenges_html) },
        { id: 'results', label: 'results', show: Boolean(project.results_html) || project.metrics.length > 0 },
        { id: 'gallery', label: 'gallery', show: screenshots.length > 0 || Boolean(project.demo_url) },
        { id: 'testimonial', label: 'feedback', show: testimonial !== null },
    ].filter((section) => section.show);

    return (
        <>
            <Seo
                title={project.seo_title ?? project.title}
                description={project.seo_description ?? project.summary}
                image={project.cover_url}
                card={shareCard.url(project.slug)}
                type="article"
            />
            <JsonLd
                id="project"
                data={{
                    '@type': 'CreativeWork',
                    name: project.title,
                    description: project.summary,
                    url: `${appUrl}${show.url(project.slug)}`,
                    author: { '@id': `${appUrl}/#person`, '@type': 'Person', name: site.name },
                    ...(project.tags.length > 0 ? { keywords: project.tags.map((tag) => tag.name).join(', ') } : {}),
                    ...(project.live_url ? { sameAs: project.live_url } : {}),
                    ...(project.cover_url ? { image: `${project.cover_url.startsWith('http') ? '' : appUrl}${project.cover_url}` } : {}),
                }}
            />

            <article>
                <PageIntro
                    label="case study"
                    title={project.title}
                    description={project.summary}
                    back={{ label: 'all projects', href: index() }}
                >
                    <Header project={project} />
                </PageIntro>

                <SectionNav sections={sections} />

                {project.problem_html && (
                    <Block id="problem" label="problem" title="The problem">
                        <Prose html={project.problem_html} />
                    </Block>
                )}

                {project.role_html && (
                    <Block id="role" label="my role" title="What I owned">
                        <Prose html={project.role_html} />
                    </Block>
                )}

                {hasArchitecture && (
                    <Block id="architecture" label="architecture" title="Architecture">
                        <div className="space-y-6">
                            {project.architecture_html && <Prose html={project.architecture_html} />}
                            {project.architecture_tree.length > 0 && (
                                <FolderTree nodes={project.architecture_tree} label="Project folder structure" />
                            )}
                            {project.diagram_url && (
                                <LoadingImage
                                    src={project.diagram_url}
                                    alt={`${project.title} architecture diagram`}
                                    frameClassName="min-h-40 w-full rounded-lg border border-border"
                                    className="h-auto"
                                />
                            )}
                        </div>
                    </Block>
                )}

                {project.decisions_html && (
                    <Block id="decisions" label="key decisions" title="Key decisions">
                        <Prose html={project.decisions_html} />
                    </Block>
                )}

                {project.challenges_html && (
                    <Block id="challenges" label="challenges" title="Challenges & solutions">
                        <Prose html={project.challenges_html} />
                    </Block>
                )}

                {(project.results_html || project.metrics.length > 0) && (
                    <Block id="results" label="results" title="Results">
                        <div className="space-y-6">
                            <ProjectMetrics metrics={project.metrics} />
                            {project.results_html && <Prose html={project.results_html} />}
                        </div>
                    </Block>
                )}

                {(screenshots.length > 0 || project.demo_url) && (
                    <Block id="gallery" label="gallery" title="Screenshots & demo" full>
                        <div className="space-y-6">
                            {project.demo_url && (
                                <Button asChild variant="outline">
                                    <a href={project.demo_url} target="_blank" rel="noreferrer">
                                        <PlayCircle aria-hidden /> Watch the demo
                                        <span className="sr-only"> (opens in a new tab)</span>
                                    </a>
                                </Button>
                            )}
                            <ProjectGallery images={screenshots} />
                        </div>
                    </Block>
                )}

                {testimonial && (
                    <Block id="testimonial" label="client feedback" title="What the client said">
                        <TestimonialCard testimonial={testimonial} showProject={false} />
                    </Block>
                )}
            </article>

            {next && (
                <nav aria-label="Next case study" className={containers.page}>
                    <div className="py-12 sm:py-16">
                        <Link
                            href={show(next.slug)}
                            className="group block rounded-lg border border-border bg-card p-6 transition-colors duration-200 hover:border-border-strong sm:p-8"
                        >
                            <SectionLabel>next case study</SectionLabel>
                            <p className="mt-3 flex items-center justify-between gap-4 text-xl font-semibold tracking-tight text-foreground">
                                {next.title}
                                <ArrowRight
                                    className="size-5 shrink-0 text-primary transition-transform duration-150 group-hover:translate-x-1"
                                    aria-hidden
                                />
                            </p>
                            <p className="mt-1 line-clamp-2 text-sm">{next.summary}</p>
                        </Link>
                    </div>
                </nav>
            )}
        </>
    );
}

function Header({ project }: { project: Project }) {
    const timeline = formatMonthRange(project.started_on, project.ended_on);
    const meta = [
        { term: 'role', value: project.role },
        { term: 'timeline', value: timeline },
    ].filter((item): item is { term: string; value: string } => Boolean(item.value));

    return (
        <div className="space-y-6">
            {(meta.length > 0 || project.tags.length > 0) && (
                <dl className="grid gap-4 rounded-lg border border-border bg-card p-4 sm:grid-cols-2">
                    {meta.map((item) => (
                        <div key={item.term} className="space-y-1">
                            <dt className="font-mono text-xs">{item.term}</dt>
                            <dd className="text-sm text-foreground">{item.value}</dd>
                        </div>
                    ))}
                    {project.tags.length > 0 && (
                        <div className="space-y-2 sm:col-span-2">
                            <dt className="font-mono text-xs">stack</dt>
                            <dd>
                                <TagList tags={project.tags} />
                            </dd>
                        </div>
                    )}
                </dl>
            )}
            <div className="flex flex-wrap items-center gap-3">
                {project.live_url && (
                    <Button asChild>
                        <a
                            href={project.live_url}
                            target="_blank"
                            rel="noreferrer"
                            onClick={() => track('Project live site', { project: project.title })}
                        >
                            <ExternalLink aria-hidden /> Live site
                            <span className="sr-only"> (opens in a new tab)</span>
                        </a>
                    </Button>
                )}
                {project.repo_url && (
                    <Button asChild variant="outline">
                        <a href={project.repo_url} target="_blank" rel="noreferrer">
                            <Github aria-hidden /> Repository
                            <span className="sr-only"> (opens in a new tab)</span>
                        </a>
                    </Button>
                )}
                {project.is_confidential && (
                    <p className="inline-flex flex-wrap items-center gap-2 font-mono text-xs">
                        <Lock className="size-3.5" aria-hidden />
                        Private client project, demo on request.
                        <Link href={contact()} className="text-primary underline-offset-4 hover:underline">
                            Ask for a walkthrough
                        </Link>
                    </p>
                )}
            </div>
        </div>
    );
}

function Block({
    id,
    label,
    title,
    full = false,
    children,
}: {
    id: string;
    label: string;
    title: string;
    /** Content spans the whole card (gallery); otherwise the heading sits in a left column on desktop. */
    full?: boolean;
    children: ReactNode;
}) {
    return (
        <section id={id} aria-labelledby={`${id}-heading`} className={cn(containers.page, 'scroll-mt-20 py-3')}>
            {/* A solid card per section keeps body text off the dot field, so it stays easy to read. */}
            <Reveal
                className={cn(
                    surface,
                    panelPadding,
                    !full && 'lg:grid lg:grid-cols-[13rem_minmax(0,1fr)] lg:gap-12',
                )}
            >
                <header className={cn('mb-6 space-y-2', !full && 'lg:sticky lg:top-24 lg:mb-0 lg:self-start')}>
                    <SectionLabel>{label}</SectionLabel>
                    <h2 id={`${id}-heading`} className="text-2xl font-semibold tracking-tight text-foreground">
                        {title}
                    </h2>
                </header>
                <div className={cn('min-w-0', !full && measure)}>{children}</div>
            </Reveal>
        </section>
    );
}

/** Jump links to the case study's sections, shown under the header. */
function SectionNav({ sections }: { sections: Array<{ id: string; label: string }> }) {
    if (sections.length < 2) {
        return null;
    }

    return (
        <nav aria-label="Case study sections" className={cn(containers.page, 'pb-6')}>
            <ul className="flex flex-wrap gap-2 font-mono text-xs">
                {sections.map((section) => (
                    <li key={section.id}>
                        <a
                            href={`#${section.id}`}
                            className="inline-flex rounded-full border border-border bg-card px-3 py-1.5 transition-colors duration-150 hover:border-border-strong hover:text-foreground"
                        >
                            {section.label}
                        </a>
                    </li>
                ))}
            </ul>
        </nav>
    );
}
