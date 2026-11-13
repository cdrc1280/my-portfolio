import { Link } from '@inertiajs/react';
import { X } from 'lucide-react';
import { Hint } from '@/components/custom/hint';
import { PageIntro } from '@/components/custom/page-intro';
import { Pagination } from '@/components/custom/pagination';
import { containers, measure, panelPadding, surface } from '@/components/custom/section';
import { Seo } from '@/components/custom/seo';
import { cn } from '@/lib/utils';
import { index } from '@/routes/blog';
import { page as shareCard } from '@/routes/og';
import type { Paginated } from '@/types';
import type { PostCard } from '@/types/domains/blog';
import { PostList } from './post-list';

type Props = {
    posts: Paginated<PostCard>;
    activeTag: string | null;
};

export default function BlogIndex({ posts, activeTag }: Props) {
    return (
        <>
            <Seo
                title={activeTag ? `Posts tagged ${activeTag}` : 'Blog'}
                description="Notes on building software: Laravel, React, architecture and shipping production systems."
                card={shareCard.url('blog')}
            />

            <PageIntro
                label="blog"
                title="Writing"
                description="Notes on building and maintaining production systems: what worked, what didn't, and why."
            >
                {activeTag && (
                    <p className="inline-flex items-center gap-2 rounded-md border border-border py-1 pr-1 pl-3 font-mono text-xs">
                        tag: <span className="text-primary">{activeTag}</span>
                        <Hint label="Clear tag filter">
                            <Link
                                href={index()}
                                className="inline-flex size-6 items-center justify-center rounded-md transition-colors duration-150 hover:bg-accent hover:text-foreground"
                                aria-label="Clear tag filter"
                            >
                                <X className="size-3.5" aria-hidden />
                            </Link>
                        </Hint>
                    </p>
                )}
            </PageIntro>

            <div className={cn(containers.page, 'pb-24')}>
                <div className={cn(surface, panelPadding, 'space-y-10')}>
                    <div className={measure}>
                        <PostList posts={posts.data} />
                    </div>
                    <Pagination paginator={posts} />
                </div>
            </div>
        </>
    );
}
