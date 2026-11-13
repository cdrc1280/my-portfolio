import { Link } from '@inertiajs/react';
import { cn } from '@/lib/utils';
import { index } from '@/routes/projects';
import type { Tag } from '@/types';

type TagFilterProps = {
    tags: Tag[];
    activeTag: string | null;
};

/**
 * Tech filter as mono badge toggles. Each is a real link (`?tag=`) so filters are shareable and
 * crawlable; `aria-current` marks the active one.
 */
export function TagFilter({ tags, activeTag }: TagFilterProps) {
    const badge = (active: boolean) =>
        cn(
            'inline-flex items-center rounded-md border px-2.5 py-1 font-mono text-xs transition-colors duration-150',
            active
                ? 'border-primary bg-primary/10 text-primary'
                : 'border-border text-muted-foreground hover:border-border-strong hover:text-foreground',
        );

    return (
        <nav aria-label="Filter projects by technology">
            <ul className="flex flex-wrap gap-2">
                <li>
                    <Link
                        href={index()}
                        preserveScroll
                        className={badge(activeTag === null)}
                        aria-current={activeTag === null ? 'page' : undefined}
                    >
                        all
                    </Link>
                </li>
                {tags.map((tag) => (
                    <li key={tag.id}>
                        <Link
                            href={activeTag === tag.slug ? index() : index({ query: { tag: tag.slug } })}
                            preserveScroll
                            className={badge(activeTag === tag.slug)}
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
