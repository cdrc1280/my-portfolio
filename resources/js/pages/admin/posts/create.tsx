import { Head } from '@inertiajs/react';
import { PageHeader } from '@/components/custom/page-header';
import { create, index } from '@/routes/admin/posts';
import type { SelectOption, Tag } from '@/types';
import type { PostStatus } from '@/types/domains/blog';
import { PostForm } from './post-form';

type Props = {
    tags: Tag[];
    statuses: SelectOption<PostStatus>[];
};

export default function CreatePost({ tags, statuses }: Props) {
    return (
        <>
            <Head title="New post" />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader title="New post" />
                <PostForm tags={tags} statuses={statuses} />
            </div>
        </>
    );
}

CreatePost.layout = {
    breadcrumbs: [
        { title: 'Blog', href: index() },
        { title: 'New', href: create() },
    ],
};
