import { Link } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { Prose } from '@/components/shared/prose';
import { Seo } from '@/components/shared/seo';
import { TagList } from '@/components/shared/tag-list';
import type { Post } from '@/features/blog/types';
import { formatDate } from '@/lib/format';
import { index } from '@/routes/blog';

export default function BlogShow({ post }: { post: Post }) {
    return (
        <>
            <Seo title={post.title} description={post.excerpt} image={post.cover_url} type="article" />
            <article className="mx-auto max-w-3xl space-y-10">
                <header className="space-y-5">
                    <Link
                        href={index()}
                        className="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground"
                    >
                        <ArrowLeft className="size-4" aria-hidden /> All posts
                    </Link>
                    <h1 className="text-3xl font-semibold tracking-tight text-balance sm:text-4xl">{post.title}</h1>
                    <p className="text-sm text-muted-foreground">
                        {post.published_at && <time dateTime={post.published_at}>{formatDate(post.published_at)}</time>}
                        {' · '}
                        {post.reading_minutes} min read
                    </p>
                    <TagList tags={post.tags} />
                </header>
                {post.cover_url && (
                    <img src={post.cover_url} alt="" className="aspect-[2/1] w-full rounded-xl border object-cover" />
                )}
                {post.body_html && <Prose html={post.body_html} className="prose-lg" />}
            </article>
        </>
    );
}
