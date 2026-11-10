import { Head, Link } from '@inertiajs/react';
import { FolderKanban, Plus } from 'lucide-react';
import { EmptyState } from '@/components/shared/empty-state';
import { PageHeader } from '@/components/shared/page-header';
import { Button } from '@/components/ui/button';
import { AdminProjectList } from '@/features/projects/components/admin-project-list';
import type { AdminProject } from '@/features/projects/types';
import { create, index } from '@/routes/admin/projects';

type Props = {
    projects: AdminProject[];
};

export default function ProjectsIndex({ projects }: Props) {
    const newButton = (
        <Button asChild>
            <Link href={create()}>
                <Plus aria-hidden /> New project
            </Link>
        </Button>
    );

    return (
        <>
            <Head title="Projects" />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Projects"
                    description="Drag to reorder. The order here is the order on the site."
                    actions={newButton}
                />
                {projects.length > 0 ? (
                    <AdminProjectList projects={projects} />
                ) : (
                    <EmptyState icon={FolderKanban} title="No projects yet" action={newButton} />
                )}
            </div>
        </>
    );
}

ProjectsIndex.layout = {
    breadcrumbs: [{ title: 'Projects', href: index() }],
};
