import { PageHeader } from '@/components/shared/page-header';
import { Seo } from '@/components/shared/seo';
import { ProjectGrid } from '@/features/projects/components/project-grid';
import { TagFilter } from '@/features/projects/components/tag-filter';
import type { ProjectCard } from '@/features/projects/types';
import type { Tag } from '@/types';

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
                description="Selected work: production systems, products and side projects."
            />
            <div className="space-y-10">
                <PageHeader
                    size="lg"
                    title="Projects"
                    description="Production systems, products and side projects I've designed and built."
                />
                {tags.length > 0 && <TagFilter tags={tags} activeTag={activeTag} />}
                <ProjectGrid projects={projects} />
            </div>
        </>
    );
}
