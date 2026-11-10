import { Link } from '@inertiajs/react';
import { ArrowUpRight } from 'lucide-react';
import { TagList } from '@/features/projects/components/tag-list';
import type { ProjectCard as ProjectCardData } from '@/features/projects/types';
import { show } from '@/routes/projects';

export function ProjectCard({ project }: { project: ProjectCardData }) {
    return (
        <article className="group relative flex flex-col overflow-hidden rounded-xl border bg-card transition-colors hover:border-foreground/20">
            <div className="aspect-[16/9] overflow-hidden bg-muted">
                {project.cover_url && (
                    <img
                        src={project.cover_url}
                        alt=""
                        loading="lazy"
                        className="size-full object-cover transition-transform duration-500 group-hover:scale-[1.02]"
                    />
                )}
            </div>
            <div className="flex flex-1 flex-col gap-3 p-5">
                <div className="space-y-1">
                    {project.period && <p className="text-xs text-muted-foreground">{project.period}</p>}
                    <h3 className="flex items-start justify-between gap-2 font-semibold tracking-tight">
                        <Link
                            href={show(project.slug)}
                            className="after:absolute after:inset-0 focus-visible:outline-none"
                        >
                            {project.title}
                        </Link>
                        <ArrowUpRight
                            className="size-4 shrink-0 text-muted-foreground transition-colors group-hover:text-brand"
                            aria-hidden
                        />
                    </h3>
                </div>
                <p className="line-clamp-3 text-sm text-muted-foreground">{project.summary}</p>
                <TagList tags={project.tags} className="mt-auto flex flex-wrap gap-1.5 pt-2" />
            </div>
        </article>
    );
}
