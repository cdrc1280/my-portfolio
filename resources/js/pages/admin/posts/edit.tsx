import { Head, Link, setLayoutProps } from '@inertiajs/react';
import { ExternalLink } from 'lucide-react';
import { PageHeader } from '@/components/shared/page-header';
import { Button } from '@/components/ui/button';
import { PostForm } from '@/features/blog/components/post-form';
import { PostStatusBadge } from '@/features/blog/components/post-status-badge';
import type { AdminPost, PostStatus } from '@/features/blog/types';
import { edit, index } from '@/routes/admin/posts';
import { show } from '@/routes/blog';
import type { SelectOption, Tag } from '@/types';

type Props = {
    post: AdminPost;
    tags: Tag[];
    statuses: SelectOption<PostStatus>[];
};

export default function EditPost({ post, tags, statuses }: Props) {
    setLayoutProps({
        breadcrumbs: [
            { title: 'Blog', href: index() },
            { title: post.title, href: edit(post.id) },
        ],
    });

    const isLive = post.status === 'published' && !post.is_scheduled;

    return (
        <>
            <Head title={`Edit ${post.title}`} />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title={post.title}
                    description={<PostStatusBadge post={post} />}
                    actions={
                        isLive && (
                            <Button variant="outline" asChild>
                                <Link href={show(post.slug)}>
                                    <ExternalLink aria-hidden /> View on site
                                </Link>
                            </Button>
                        )
                    }
                />
                <PostForm key={post.updated_at} post={post} tags={tags} statuses={statuses} />
            </div>
        </>
    );
}
