import { usePage } from '@inertiajs/react';
import { LoadingImage } from '@/components/custom/loading-image';
import { PageIntro } from '@/components/custom/page-intro';
import { Prose } from '@/components/custom/prose';
import { Reveal } from '@/components/custom/reveal';
import { containers, panelPadding, surface } from '@/components/custom/section';
import { JsonLd, Seo } from '@/components/custom/seo';
import { TagList } from '@/components/custom/tag-list';
import { formatDate } from '@/lib/format';
import { cn } from '@/lib/utils';
import { index, show } from '@/routes/blog';
import { post as shareCard } from '@/routes/og';
import type { Post } from '@/types/domains/blog';

export default function BlogShow({ post }: { post: Post }) {
    const { site, appUrl } = usePage().props;
    return (
        <>
            <Seo
                title={post.seo_title ?? post.title}
                description={post.seo_description ?? post.excerpt}
                image={post.cover_url}
                card={shareCard.url(post.slug)}
                type="article"
            />
            <JsonLd
                id="post"
                data={{
                    '@type': 'BlogPosting',
                    headline: post.title,
                    description: post.excerpt,
                    url: `${appUrl}${show.url(post.slug)}`,
                    author: { '@id': `${appUrl}/#person`, '@type': 'Person', name: site.name },
                    ...(post.published_at ? { datePublished: post.published_at } : {}),
                    ...(post.updated_at ? { dateModified: post.updated_at } : {}),
                    ...(post.tags.length > 0 ? { keywords: post.tags.map((tag) => tag.name).join(', ') } : {}),
                    image: `${appUrl}${post.cover_url ?? shareCard.url(post.slug)}`,
                }}
            />

            <article>
                <PageIntro label="post" title={post.title} back={{ label: 'all posts', href: index() }} width="article">
                    <div className="space-y-4">
                        <p className="font-mono text-xs">
                            {post.published_at && <time dateTime={post.published_at}>{formatDate(post.published_at)}</time>}
                            <span aria-hidden className="mx-2 text-border-strong">
                                /
                            </span>
                            {post.reading_minutes} min read
                        </p>
                        <TagList tags={post.tags} />
                    </div>
                </PageIntro>

                <div className={cn(containers.article, 'pb-24')}>
                    {post.cover_url && (
                        <Reveal className="mb-6">
                            <LoadingImage
                                src={post.cover_url}
                                alt=""
                                loading="eager"
                                frameClassName="aspect-[2/1] w-full rounded-lg border border-border"
                                className="object-cover"
                            />
                        </Reveal>
                    )}
                    {post.body_html && (
                        <div className={cn(surface, panelPadding)}>
                            <Prose html={post.body_html} />
                        </div>
                    )}
                </div>
            </article>
        </>
    );
}
