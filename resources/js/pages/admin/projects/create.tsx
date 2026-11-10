import { Head } from '@inertiajs/react';
import { PageHeader } from '@/components/shared/page-header';
import { ProjectForm } from '@/features/projects/components/project-form';
import type { ProjectStatus } from '@/features/projects/types';
import { create, index } from '@/routes/admin/projects';
import type { SelectOption, Tag } from '@/types';

type Props = {
    tags: Tag[];
    statuses: SelectOption<ProjectStatus>[];
};

export default function CreateProject({ tags, statuses }: Props) {
    return (
        <>
            <Head title="New project" />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader title="New project" />
                <ProjectForm tags={tags} statuses={statuses} />
            </div>
        </>
    );
}

CreateProject.layout = {
    breadcrumbs: [
        { title: 'Projects', href: index() },
        { title: 'New', href: create() },
    ],
};
