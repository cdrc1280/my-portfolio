import { Link } from '@inertiajs/react';
import { Newspaper } from 'lucide-react';
import { EmptyState } from '@/components/shared/empty-state';
import { TagList } from '@/components/shared/tag-list';
import type { PostCard } from '@/features/blog/types';
import { formatDate } from '@/lib/format';
import { show } from '@/routes/blog';

/** Text-first list of posts; covers are omitted to keep it scannable. */
export function PostList({ posts }: { posts: PostCard[] }) {
    if (posts.length === 0) {
        return <EmptyState icon={Newspaper} title="No posts yet" description="Check back soon." />;
    }

    return (
        <ul className="divide-y">
            {posts.map((post) => (
                <li key={post.id} className="py-8 first:pt-0">
                    <article className="group relative space-y-2">
                        <p className="text-sm text-muted-foreground">
                            {post.published_at && (
                                <time dateTime={post.published_at}>{formatDate(post.published_at)}</time>
                            )}
                            {' · '}
                            {post.reading_minutes} min read
                        </p>
                        <h2 className="text-xl font-semibold tracking-tight">
                            <Link
                                href={show(post.slug)}
                                className="group-hover:text-brand after:absolute after:inset-0"
                            >
                                {post.title}
                            </Link>
                        </h2>
                        <p className="text-muted-foreground">{post.excerpt}</p>
                        <TagList tags={post.tags} className="flex flex-wrap gap-1.5 pt-1" />
                    </article>
                </li>
            ))}
        </ul>
    );
}
