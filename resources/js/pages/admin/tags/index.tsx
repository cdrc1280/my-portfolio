import { Head } from '@inertiajs/react';
import { PageHeader } from '@/components/shared/page-header';
import { CreateTagForm } from '@/features/tags/components/create-tag-form';
import { TagTable } from '@/features/tags/components/tag-table';
import type { AdminTag } from '@/features/tags/types';
import { index } from '@/routes/admin/tags';

type Props = {
    tags: AdminTag[];
};

export default function TagsIndex({ tags }: Props) {
    return (
        <>
            <Head title="Tags" />
            <div className="flex max-w-3xl flex-col gap-6 p-4 md:p-6">
                <PageHeader title="Tags" description="Technologies used to label and filter projects." />
                <CreateTagForm />
                <TagTable tags={tags} />
            </div>
        </>
    );
}

TagsIndex.layout = {
    breadcrumbs: [{ title: 'Tags', href: index() }],
};
