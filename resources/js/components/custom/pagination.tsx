import { Link } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { Button } from '@/components/ui/button';
import type { Paginated } from '@/types';

/** Previous / next pager for a Laravel paginated resource. */
export function Pagination<TItem>({ paginator }: { paginator: Paginated<TItem> }) {
    const { meta, links } = paginator;

    if (meta.last_page <= 1) {
        return null;
    }

    return (
        <nav aria-label="Pagination" className="flex items-center justify-between gap-4 text-sm">
            <p className="text-muted-foreground">
                {meta.from}–{meta.to} of {meta.total}
            </p>
            <div className="flex gap-2">
                <Button variant="outline" size="sm" asChild={links.prev !== null} disabled={links.prev === null}>
                    {links.prev ? (
                        <Link href={links.prev} preserveScroll rel="prev">
                            <ChevronLeft aria-hidden /> Previous
                        </Link>
                    ) : (
                        <span>
                            <ChevronLeft aria-hidden /> Previous
                        </span>
                    )}
                </Button>
                <Button variant="outline" size="sm" asChild={links.next !== null} disabled={links.next === null}>
                    {links.next ? (
                        <Link href={links.next} preserveScroll rel="next">
                            Next <ChevronRight aria-hidden />
                        </Link>
                    ) : (
                        <span>
                            Next <ChevronRight aria-hidden />
                        </span>
                    )}
                </Button>
            </div>
        </nav>
    );
}
