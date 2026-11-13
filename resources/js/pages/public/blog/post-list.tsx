import { Link } from '@inertiajs/react';
import { ArrowRight, Newspaper } from 'lucide-react';
import { EmptyState } from '@/components/custom/empty-state';
import { Reveal } from '@/components/custom/reveal';
import { TagList } from '@/components/custom/tag-list';
import { formatDate } from '@/lib/format';
import { show } from '@/routes/blog';
import type { PostCard } from '@/types/domains/blog';

/** Text-first post rows: mono date and reading time, title, excerpt and tags. */
export function PostList({ posts, headingLevel = 'h2' }: { posts: PostCard[]; headingLevel?: 'h2' | 'h3' }) {
    if (posts.length === 0) {
        return <EmptyState icon={Newspaper} title="No posts yet" description="Check back soon." />;
    }

    const Heading = headingLevel;

    return (
        <ul className="divide-y divide-border border-y border-border">
            {posts.map((post) => (
                <li key={post.id}>
                    <Reveal>
                        <article className="group relative grid gap-2 py-8 sm:grid-cols-[9rem_1fr] sm:gap-8">
                            <p className="font-mono text-xs text-muted-foreground sm:pt-1">
                                {post.published_at && (
                                    <time dateTime={post.published_at}>{formatDate(post.published_at)}</time>
                                )}
                                <span className="block">{post.reading_minutes} min read</span>
                            </p>
                            <div className="space-y-2">
                                <Heading className="text-lg font-semibold tracking-tight text-foreground transition-colors duration-150 group-hover:text-primary">
                                    <Link href={show(post.slug)} className="outline-none after:absolute after:inset-0">
                                        {post.title}
                                    </Link>
                                </Heading>
                                <p className="leading-relaxed">{post.excerpt}</p>
                                <TagList tags={post.tags} className="pt-1" />
                                <span className="inline-flex items-center gap-1 pt-1 text-sm font-medium text-primary">
                                    Read post
                                    <ArrowRight
                                        className="size-4 transition-transform duration-150 group-hover:translate-x-0.5"
                                        aria-hidden
                                    />
                                </span>
                            </div>
                        </article>
                    </Reveal>
                </li>
            ))}
        </ul>
    );
}
