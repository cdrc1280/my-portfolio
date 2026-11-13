import { PageIntro } from '@/components/custom/page-intro';
import { ProjectGrid } from '@/components/custom/projects/project-grid';
import { Reveal } from '@/components/custom/reveal';
import { containers, surface } from '@/components/custom/section';
import { Seo } from '@/components/custom/seo';
import { cn } from '@/lib/utils';
import { page as shareCard } from '@/routes/og';
import type { Tag } from '@/types';
import type { ProjectCard } from '@/types/domains/projects';
import { TagFilter } from './tag-filter';

type Props = {
    projects: ProjectCard[];
    tags: Tag[];
    activeTag: string | null;
};

export default function ProjectsIndex({ projects, tags, activeTag }: Props) {
    const activeName = tags.find((tag) => tag.slug === activeTag)?.name;

    return (
        <>
            <Seo
                title={activeName ? `${activeName} projects` : 'Projects'}
                description="Case studies of production systems built with Laravel, React and Vue: the problem, the architecture and the result."
                card={shareCard.url('projects')}
            />

            <PageIntro
                label="projects"
                title="Projects"
                description="Production systems and products I've designed, built and maintained. Each one links to a case study."
            />

            <div className={cn(containers.page, 'space-y-8 pb-24')}>
                {tags.length > 0 && (
                    <Reveal className={cn(surface, 'space-y-3 p-4 sm:p-5')}>
                        <p className="font-mono text-xs" aria-live="polite">
                            {projects.length} {projects.length === 1 ? 'project' : 'projects'}
                            {activeName && ` using ${activeName}`}
                        </p>
                        <TagFilter tags={tags} activeTag={activeTag} />
                    </Reveal>
                )}
                <ProjectGrid projects={projects} headingLevel="h2" />
            </div>
        </>
    );
}
