import { Head, Link } from '@inertiajs/react';
import { FolderKanban, Plus } from 'lucide-react';
import { EmptyState } from '@/components/custom/empty-state';
import { PageHeader } from '@/components/custom/page-header';
import { Button } from '@/components/ui/button';
import { create, index } from '@/routes/admin/projects';
import type { AdminProject } from '@/types/domains/projects';
import { AdminProjectList } from './admin-project-list';

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
