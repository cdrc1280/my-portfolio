import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import { index } from '@/routes/projects';
import type { Tag } from '@/types';

type TagFilterProps = {
    tags: Tag[];
    activeTag: string | null;
};

/** Filter chips; each is a real link so filters are shareable and crawlable. */
export function TagFilter({ tags, activeTag }: TagFilterProps) {
    const chip = (active: boolean) =>
        cn(
            'inline-flex rounded-full border px-3 py-1 text-sm transition-colors',
            active
                ? 'border-foreground bg-foreground text-background'
                : 'text-muted-foreground hover:border-foreground/30 hover:text-foreground',
        );

    return (
        <nav aria-label="Filter by technology">
            <ul className="flex flex-wrap gap-2">
                <li>
                    <Link
                        href={index()}
                        preserveScroll
                        className={chip(activeTag === null)}
                        aria-current={activeTag === null ? 'page' : undefined}
                    >
                        All
                    </Link>
                </li>
                {tags.map((tag) => (
                    <li key={tag.id}>
                        <Link
                            href={index({ query: { tag: tag.slug } })}
                            preserveScroll
                            className={chip(activeTag === tag.slug)}
                            aria-current={activeTag === tag.slug ? 'page' : undefined}
                        >
                            {tag.name}
                        </Link>
                    </li>
                ))}
            </ul>
        </nav>
    );
}
