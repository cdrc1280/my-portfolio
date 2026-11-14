import { Skeleton } from '@/components/ui/skeleton';

type Variant = 'dashboard' | 'form' | 'detail' | 'list';

/** Picks the skeleton that matches the admin page being loaded, from its pathname. */
function variantFor(path: string): Variant {
    if (path === '/admin' || path === '/admin/') return 'dashboard';
    if (/\/(create|edit)$/.test(path) || path === '/admin/profile' || path.startsWith('/settings')) return 'form';
    if (/^\/admin\/messages\/\d+$/.test(path)) return 'detail';

    return 'list';
}

/** Placeholder for a slow admin navigation, shaped like the page it is loading. */
export function AdminPageSkeleton({ path }: { path: string }) {
    const variant = variantFor(path);

    return (
        <div role="status" aria-busy="true" className="flex flex-col gap-6 p-4 md:p-6">
            <span className="sr-only">Loading page…</span>
            <div aria-hidden className="flex flex-col gap-6">
                <div className="flex items-end justify-between gap-4">
                    <div className="space-y-2">
                        <Skeleton className="h-7 w-56" />
                        <Skeleton className="h-4 w-80 max-w-full" />
                    </div>
                    {variant !== 'detail' && <Skeleton className="h-9 w-28" />}
                </div>

                {variant === 'dashboard' && (
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12">
                        <Skeleton className="h-80 rounded-xl md:col-span-2 xl:col-span-8 xl:row-span-2 xl:h-auto" />
                        <Skeleton className="h-48 rounded-xl xl:col-span-4" />
                        <Skeleton className="h-48 rounded-xl xl:col-span-4" />
                        {[0, 1, 2, 3].map((key) => (
                            <Skeleton key={key} className="h-28 rounded-xl xl:col-span-3" />
                        ))}
                        <Skeleton className="h-72 rounded-xl xl:col-span-6" />
                        <Skeleton className="h-72 rounded-xl xl:col-span-6" />
                    </div>
                )}

                {variant === 'list' && (
                    <div className="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
                        <div className="space-y-2">
                            {[0, 1, 2, 3, 4, 5].map((key) => (
                                <div key={key} className="flex items-center gap-3 rounded-lg border p-3">
                                    <Skeleton className="size-5" />
                                    <div className="flex-1 space-y-1.5">
                                        <Skeleton className="h-4 w-1/2" />
                                        <Skeleton className="h-3 w-1/3" />
                                    </div>
                                    <Skeleton className="size-8" />
                                    <Skeleton className="size-8" />
                                </div>
                            ))}
                        </div>
                        <div className="hidden content-start gap-4 xl:grid">
                            <Skeleton className="h-44 rounded-xl" />
                            <Skeleton className="h-32 rounded-xl" />
                        </div>
                    </div>
                )}

                {variant === 'form' && (
                    <div className="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
                        <div className="space-y-6">
                            {[0, 1].map((card) => (
                                <div key={card} className="space-y-5 rounded-xl border p-6">
                                    <Skeleton className="h-5 w-32" />
                                    <div className="grid gap-5 sm:grid-cols-2">
                                        {[0, 1, 2, 3].map((field) => (
                                            <div key={field} className="space-y-2">
                                                <Skeleton className="h-3.5 w-24" />
                                                <Skeleton className="h-9 w-full" />
                                            </div>
                                        ))}
                                    </div>
                                    <Skeleton className="h-28 w-full" />
                                </div>
                            ))}
                        </div>
                        <div className="hidden content-start gap-4 xl:grid">
                            <Skeleton className="h-44 rounded-xl" />
                            <Skeleton className="h-32 rounded-xl" />
                        </div>
                    </div>
                )}

                {variant === 'detail' && (
                    <div className="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
                        <div className="space-y-4 rounded-xl border p-6">
                            <Skeleton className="h-6 w-1/2" />
                            <Skeleton className="h-4 w-1/3" />
                            <div className="grid gap-3 sm:grid-cols-3">
                                {[0, 1, 2].map((key) => (
                                    <Skeleton key={key} className="h-14" />
                                ))}
                            </div>
                            <Skeleton className="h-32 w-full" />
                        </div>
                        <div className="hidden content-start gap-4 xl:grid">
                            <Skeleton className="h-44 rounded-xl" />
                            <Skeleton className="h-32 rounded-xl" />
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
