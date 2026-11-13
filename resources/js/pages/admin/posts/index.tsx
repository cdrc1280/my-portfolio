import { Head, Link, router } from '@inertiajs/react';
import { Newspaper, Pencil, Plus, Trash2 } from 'lucide-react';
import { ConfirmDialog } from '@/components/custom/confirm-dialog';
import { EmptyState } from '@/components/custom/empty-state';
import { PageHeader } from '@/components/custom/page-header';
import { Pagination } from '@/components/custom/pagination';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { formatDate } from '@/lib/format';
import { create, destroy, edit, index } from '@/routes/admin/posts';
import type { Paginated } from '@/types';
import type { AdminPost } from '@/types/domains/blog';
import { PostStatusBadge } from './post-status-badge';

export default function PostsIndex({ posts }: { posts: Paginated<AdminPost> }) {
    const newButton = (
        <Button asChild>
            <Link href={create()}>
                <Plus aria-hidden /> New post
            </Link>
        </Button>
    );

    return (
        <>
            <Head title="Blog" />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader title="Blog" description="Drafts first, then newest published." actions={newButton} />
                {posts.data.length === 0 ? (
                    <EmptyState icon={Newspaper} title="No posts yet" action={newButton} />
                ) : (
                    <div className="rounded-xl border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Title</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead className="hidden md:table-cell">Date</TableHead>
                                    <TableHead className="w-24">
                                        <span className="sr-only">Actions</span>
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {posts.data.map((post) => (
                                    <TableRow key={post.id}>
                                        <TableCell className="max-w-md">
                                            <Link
                                                href={edit(post.id)}
                                                className="block truncate font-medium hover:underline"
                                            >
                                                {post.title}
                                            </Link>
                                        </TableCell>
                                        <TableCell>
                                            <PostStatusBadge post={post} />
                                        </TableCell>
                                        <TableCell className="hidden text-muted-foreground md:table-cell">
                                            {post.published_at ? formatDate(post.published_at) : '—'}
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex justify-end gap-1">
                                                <Button variant="ghost" size="icon" asChild>
                                                    <Link href={edit(post.id)} aria-label={`Edit ${post.title}`}>
                                                        <Pencil aria-hidden />
                                                    </Link>
                                                </Button>
                                                <ConfirmDialog
                                                    title={`Delete "${post.title}"?`}
                                                    description="This cannot be undone."
                                                    confirmLabel="Delete"
                                                    onConfirm={() =>
                                                        router.delete(destroy.url(post.id), { preserveScroll: true })
                                                    }
                                                    trigger={
                                                        <Button
                                                            variant="ghost"
                                                            size="icon"
                                                            aria-label={`Delete ${post.title}`}
                                                        >
                                                            <Trash2 aria-hidden />
                                                        </Button>
                                                    }
                                                />
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                )}
                <Pagination paginator={posts} />
            </div>
        </>
    );
}

PostsIndex.layout = {
    breadcrumbs: [{ title: 'Blog', href: index() }],
};
