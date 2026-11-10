import { FolderOpen } from 'lucide-react';
import { EmptyState } from '@/components/shared/empty-state';
import { ProjectCard } from '@/features/projects/components/project-card';
import type { ProjectCard as ProjectCardData } from '@/features/projects/types';

export function ProjectGrid({ projects }: { projects: ProjectCardData[] }) {
    if (projects.length === 0) {
        return <EmptyState icon={FolderOpen} title="No projects found" description="Try another filter." />;
    }

    return (
        <div className="grid gap-6 sm:grid-cols-2">
            {projects.map((project) => (
                <ProjectCard key={project.id} project={project} />
            ))}
        </div>
    );
}
