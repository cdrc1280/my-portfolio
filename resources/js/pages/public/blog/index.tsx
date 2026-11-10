import { Link } from '@inertiajs/react';
import { X } from 'lucide-react';
import { PageHeader } from '@/components/shared/page-header';
import { Pagination } from '@/components/shared/pagination';
import { Seo } from '@/components/shared/seo';
import { PostList } from '@/features/blog/components/post-list';
import type { PostCard } from '@/features/blog/types';
import { index } from '@/routes/blog';
import type { Paginated } from '@/types';

type Props = {
    posts: Paginated<PostCard>;
    activeTag: string | null;
};

export default function BlogIndex({ posts, activeTag }: Props) {
    return (
        <>
            <Seo title="Blog" description="Notes on building software: Laravel, React and shipping production systems." />
            <div className="mx-auto max-w-3xl space-y-10">
                <PageHeader size="lg" title="Blog" description="Notes on building and shipping software." />
                {activeTag && (
                    <p className="flex items-center gap-2 text-sm">
                        Tagged <span className="font-medium">{activeTag}</span>
                        <Link href={index()} className="inline-flex items-center text-muted-foreground hover:text-foreground">
                            <X className="size-4" aria-hidden />
                            <span className="sr-only">Clear filter</span>
                        </Link>
                    </p>
                )}
                <PostList posts={posts.data} />
                <Pagination paginator={posts} />
            </div>
        </>
    );
}
