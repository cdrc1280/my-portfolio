import { containers, measure } from '@/components/custom/section';
import { Skeleton } from '@/components/ui/skeleton';
import { cn } from '@/lib/utils';

type Variant = 'home' | 'projects' | 'project' | 'posts' | 'post' | 'about' | 'contact' | 'page';

/** Picks the skeleton that matches the page being loaded, from its pathname. */
function variantFor(path: string): Variant {
    if (path === '/') return 'home';
    if (path === '/projects') return 'projects';
    if (path.startsWith('/projects/')) return 'project';
    if (path === '/blog') return 'posts';
    if (path.startsWith('/blog/')) return 'post';
    if (path === '/about') return 'about';
    if (path === '/contact') return 'contact';

    return 'page';
}

/**
 * Placeholder layout shown while a slow page navigation loads (see usePageTransition). Each variant
 * mirrors the real page's structure so nothing jumps when the content arrives.
 */
export function PageSkeleton({ path }: { path: string }) {
    const variant = variantFor(path);
    // Blog posts are the only centered reading column; everything else shares the page container.
    const container = variant === 'post' ? containers.article : containers.page;
    const textOnly = variant === 'posts' || variant === 'page';

    return (
        <div role="status" aria-busy="true" aria-live="polite">
            <span className="sr-only">Loading page…</span>
            <div aria-hidden>
                <IntroSkeleton container={container} hero={variant === 'home'} />
                <div className={cn(container, 'pb-24')}>
                    <div className={textOnly ? measure : undefined}>
                        {variant === 'home' && <CardGridSkeleton count={3} />}
                        {variant === 'projects' && (
                            <div className="space-y-8">
                                <div className="flex flex-wrap gap-2">
                                    {['w-11', 'w-16', 'w-14', 'w-18', 'w-15', 'w-13', 'w-20'].map((width) => (
                                        <Skeleton key={width} className={cn('h-7', width)} />
                                    ))}
                                </div>
                                <CardGridSkeleton count={6} />
                            </div>
                        )}
                        {variant === 'project' && (
                            <div className="space-y-6">
                                <Skeleton className={cn('h-28 w-full rounded-lg', measure)} />
                                {[0, 1].map((key) => (
                                    <div
                                        key={key}
                                        className="gap-12 rounded-2xl border border-border bg-card p-6 sm:p-8 lg:grid lg:grid-cols-[13rem_minmax(0,1fr)] lg:p-10"
                                    >
                                        <div className="mb-6 space-y-2 lg:mb-0">
                                            <Skeleton className="h-3 w-16" />
                                            <Skeleton className="h-7 w-36" />
                                        </div>
                                        <div className={measure}>
                                            <TextBlockSkeleton />
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                        {variant === 'posts' && <RowListSkeleton />}
                        {(variant === 'post' || variant === 'page') && (
                            <div className="space-y-10">
                                <TextBlockSkeleton />
                                <TextBlockSkeleton />
                            </div>
                        )}
                        {variant === 'about' && (
                            <div className="grid gap-8 border-t border-border pt-12 sm:grid-cols-[10rem_1fr]">
                                <Skeleton className="aspect-[4/5] w-32 rounded-lg sm:w-40" />
                                <TextBlockSkeleton />
                            </div>
                        )}
                        {variant === 'contact' && (
                            <div className="grid gap-10 lg:grid-cols-[1fr_18rem] lg:gap-16">
                                <div className="space-y-5 rounded-lg border border-border bg-card p-5 sm:p-8">
                                    <div className="grid gap-5 sm:grid-cols-2">
                                        <FieldSkeleton />
                                        <FieldSkeleton />
                                        <FieldSkeleton />
                                        <FieldSkeleton />
                                    </div>
                                    <FieldSkeleton tall />
                                    <Skeleton className="h-10 w-36" />
                                </div>
                                <div className="space-y-5">
                                    {[0, 1, 2, 3].map((key) => (
                                        <div key={key} className="space-y-2">
                                            <Skeleton className="h-3 w-24" />
                                            <Skeleton className="h-4 w-48" />
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

function IntroSkeleton({ container, hero }: { container: string; hero: boolean }) {
    return (
        <div className={cn(container, hero ? 'py-20 sm:py-28' : 'pt-16 pb-12 sm:pt-20')}>
            <div className="max-w-3xl space-y-5">
                <Skeleton className="h-3 w-28" />
                <div className="space-y-3">
                    <Skeleton className="h-9 w-4/5 md:h-11" />
                    {hero && <Skeleton className="h-9 w-3/5 md:h-11" />}
                </div>
                <div className="space-y-2">
                    <Skeleton className="h-4 w-full max-w-xl" />
                    <Skeleton className="h-4 w-2/3 max-w-md" />
                </div>
                {hero && (
                    <div className="flex gap-3 pt-2">
                        <Skeleton className="h-10 w-32" />
                        <Skeleton className="h-10 w-24" />
                    </div>
                )}
            </div>
        </div>
    );
}

function CardGridSkeleton({ count }: { count: number }) {
    return (
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {Array.from({ length: count }, (_, index) => (
                <div key={index} className="space-y-4 rounded-lg border border-border bg-card p-5">
                    <Skeleton className="h-3 w-20" />
                    <Skeleton className="h-5 w-3/4" />
                    <div className="space-y-2">
                        <Skeleton className="h-3.5 w-full" />
                        <Skeleton className="h-3.5 w-5/6" />
                    </div>
                    <div className="flex gap-1.5">
                        <Skeleton className="h-5 w-14" />
                        <Skeleton className="h-5 w-16" />
                        <Skeleton className="h-5 w-12" />
                    </div>
                    <div className="border-t border-border pt-4">
                        <Skeleton className="h-4 w-24" />
                    </div>
                </div>
            ))}
        </div>
    );
}

function RowListSkeleton() {
    return (
        <div className="divide-y divide-border border-y border-border">
            {[0, 1, 2].map((key) => (
                <div key={key} className="grid gap-2 py-8 sm:grid-cols-[9rem_1fr] sm:gap-8">
                    <Skeleton className="h-3 w-24" />
                    <div className="space-y-3">
                        <Skeleton className="h-5 w-3/4" />
                        <Skeleton className="h-4 w-full" />
                        <Skeleton className="h-4 w-2/3" />
                    </div>
                </div>
            ))}
        </div>
    );
}

function TextBlockSkeleton() {
    return (
        <div className="space-y-3 border-t border-border pt-12">
            <Skeleton className="h-3 w-20" />
            <Skeleton className="h-6 w-48" />
            <div className="space-y-2 pt-2">
                <Skeleton className="h-4 w-full" />
                <Skeleton className="h-4 w-full" />
                <Skeleton className="h-4 w-3/4" />
            </div>
        </div>
    );
}

function FieldSkeleton({ tall = false }: { tall?: boolean }) {
    return (
        <div className="space-y-2">
            <Skeleton className="h-3.5 w-20" />
            <Skeleton className={cn('w-full', tall ? 'h-32' : 'h-9')} />
        </div>
    );
}
