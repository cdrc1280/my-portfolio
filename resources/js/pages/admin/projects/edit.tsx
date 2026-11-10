import { Head, Link, setLayoutProps } from '@inertiajs/react';
import { ExternalLink } from 'lucide-react';
import { PageHeader } from '@/components/shared/page-header';
import { Button } from '@/components/ui/button';
import { ProjectForm } from '@/features/projects/components/project-form';
import { ProjectStatusBadge } from '@/features/projects/components/project-status-badge';
import type { AdminProject, ProjectStatus } from '@/features/projects/types';
import { edit, index } from '@/routes/admin/projects';
import { show } from '@/routes/projects';
import type { SelectOption, Tag } from '@/types';

type Props = {
    project: AdminProject;
    tags: Tag[];
    statuses: SelectOption<ProjectStatus>[];
};

export default function EditProject({ project, tags, statuses }: Props) {
    setLayoutProps({
        breadcrumbs: [
            { title: 'Projects', href: index() },
            { title: project.title, href: edit(project.id) },
        ],
    });

    return (
        <>
            <Head title={`Edit ${project.title}`} />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title={project.title}
                    description={<ProjectStatusBadge status={project.status} />}
                    actions={
                        project.status === 'published' && (
                            <Button variant="outline" asChild>
                                <Link href={show(project.slug)}>
                                    <ExternalLink aria-hidden /> View on site
                                </Link>
                            </Button>
                        )
                    }
                />
                <ProjectForm key={project.updated_at} project={project} tags={tags} statuses={statuses} />
            </div>
        </>
    );
}
