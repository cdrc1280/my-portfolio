import { FolderOpen } from 'lucide-react';
import { EmptyState } from '@/components/custom/empty-state';
import { ProjectCard } from '@/components/custom/project-card';
import { Reveal } from '@/components/custom/reveal';
import { formatMonthRange } from '@/lib/format';
import { cn } from '@/lib/utils';
import { show } from '@/routes/projects';
import type { ProjectCard as ProjectCardData } from '@/types/domains/projects';

/** Responsive grid of ProjectCards: 1 column on phones, 2 on tablets, up to 3 on desktop. */
export function ProjectGrid({
    projects,
    columns = 3,
    headingLevel = 'h3',
}: {
    projects: ProjectCardData[];
    columns?: 2 | 3;
    headingLevel?: 'h2' | 'h3';
}) {
    if (projects.length === 0) {
        return <EmptyState icon={FolderOpen} title="No projects found" description="Try another filter." />;
    }

    return (
        <ul className={cn('grid gap-4 sm:grid-cols-2', columns === 3 && 'lg:grid-cols-3')}>
            {projects.map((project, index) => (
                <li key={project.id} className="flex">
                    <Reveal delay={(index % columns) * 0.06} className="flex w-full">
                        <ProjectCard
                            title={project.title}
                            summary={project.summary}
                            href={show(project.slug)}
                            tags={project.tags.map((tag) => tag.name)}
                            period={formatMonthRange(project.started_on, project.ended_on)}
                            liveUrl={project.live_url}
                            repoUrl={project.repo_url}
                            confidential={project.is_confidential}
                            headingLevel={headingLevel}
                        />
                    </Reveal>
                </li>
            ))}
        </ul>
    );
}
