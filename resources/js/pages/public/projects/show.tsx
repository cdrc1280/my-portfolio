import { Link } from '@inertiajs/react';
import { ArrowLeft, ExternalLink, Github } from 'lucide-react';
import { Prose } from '@/components/shared/prose';
import { Seo } from '@/components/shared/seo';
import { TagList } from '@/components/shared/tag-list';
import { Button } from '@/components/ui/button';
import { ProjectGallery } from '@/features/projects/components/project-gallery';
import { ProjectGrid } from '@/features/projects/components/project-grid';
import type { Project, ProjectCard } from '@/features/projects/types';
import { index } from '@/routes/projects';

type Props = {
    project: Project;
    related: ProjectCard[];
};

export default function ProjectShow({ project, related }: Props) {
    return (
        <>
            <Seo title={project.title} description={project.summary} image={project.cover_url} type="article" />

            <article className="space-y-12">
                <header className="space-y-6">
                    <Link
                        href={index()}
                        className="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground"
                    >
                        <ArrowLeft className="size-4" aria-hidden /> All projects
                    </Link>
                    <div className="space-y-3">
                        <h1 className="text-3xl font-semibold tracking-tight text-balance sm:text-4xl">{project.title}</h1>
                        <p className="max-w-2xl text-lg text-muted-foreground">{project.summary}</p>
                    </div>

                    <dl className="flex flex-wrap gap-x-10 gap-y-3 text-sm">
                        {project.role && <Meta label="Role" value={project.role} />}
                        {project.period && <Meta label="Period" value={project.period} />}
                    </dl>

                    <TagList tags={project.tags} />

                    {(project.live_url || project.repo_url) && (
                        <div className="flex flex-wrap gap-2">
                            {project.live_url && (
                                <Button asChild>
                                    <a href={project.live_url} target="_blank" rel="noreferrer">
                                        <ExternalLink aria-hidden /> Visit site
                                    </a>
                                </Button>
                            )}
                            {project.repo_url && (
                                <Button variant="outline" asChild>
                                    <a href={project.repo_url} target="_blank" rel="noreferrer">
                                        <Github aria-hidden /> Source
                                    </a>
                                </Button>
                            )}
                        </div>
                    )}
                </header>

                {project.cover_url && (
                    <img
                        src={project.cover_url}
                        alt={`${project.title} cover`}
                        className="aspect-[16/9] w-full rounded-xl border object-cover"
                    />
                )}

                <div className="grid gap-12 lg:grid-cols-[minmax(0,1fr)_16rem]">
                    {project.body_html ? <Prose html={project.body_html} /> : <div />}

                    {project.highlights.length > 0 && (
                        <aside aria-labelledby="highlights-heading" className="space-y-3">
                            <h2 id="highlights-heading" className="text-sm font-semibold tracking-tight">
                                Highlights
                            </h2>
                            <ul className="space-y-2 text-sm text-muted-foreground">
                                {project.highlights.map((highlight) => (
                                    <li key={highlight} className="border-l-2 border-brand/60 pl-3">
                                        {highlight}
                                    </li>
                                ))}
                            </ul>
                        </aside>
                    )}
                </div>

                <ProjectGallery images={project.images} />
            </article>

            {related.length > 0 && (
                <section aria-labelledby="related-heading" className="mt-20 space-y-6 border-t pt-12">
                    <h2 id="related-heading" className="text-xl font-semibold tracking-tight">
                        Related projects
                    </h2>
                    <ProjectGrid projects={related} />
                </section>
            )}
        </>
    );
}

function Meta({ label, value }: { label: string; value: string }) {
    return (
        <div>
            <dt className="text-muted-foreground">{label}</dt>
            <dd className="font-medium">{value}</dd>
        </div>
    );
}
