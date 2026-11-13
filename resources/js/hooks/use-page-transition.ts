import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';

/** Once shown, a skeleton stays at least this long so it never flickers. */
const MIN_VISIBLE_MS = 300;

/**
 * The pathname of a page navigation that is taking longer than `delay` ms, or null.
 *
 * Only real page changes count: GET visits to a different path that reset the scroll. Filter
 * clicks and pagination (`preserveScroll`), form posts, prefetches, partial reloads and deferred
 * props never trigger it, and instant (prefetched) visits finish before the delay.
 */
export function usePageTransition(delay = 120): string | null {
    const [pendingPath, setPendingPath] = useState<string | null>(null);

    useEffect(() => {
        let showTimer: number | undefined;
        let hideTimer: number | undefined;
        let shownAt: number | null = null;

        const offStart = router.on('start', (event) => {
            const { visit } = event.detail;
            const isPageChange =
                visit.method === 'get' &&
                !visit.prefetch &&
                visit.preserveScroll === false &&
                visit.only.length === 0 &&
                visit.url.pathname !== window.location.pathname;

            if (!isPageChange) {
                return;
            }

            window.clearTimeout(showTimer);
            window.clearTimeout(hideTimer);
            showTimer = window.setTimeout(() => {
                shownAt = Date.now();
                window.scrollTo({ top: 0 });
                setPendingPath(visit.url.pathname);
            }, delay);
        });

        const offFinish = router.on('finish', () => {
            window.clearTimeout(showTimer);

            if (shownAt === null) {
                return;
            }

            const remaining = Math.max(0, MIN_VISIBLE_MS - (Date.now() - shownAt));
            hideTimer = window.setTimeout(() => {
                shownAt = null;
                setPendingPath(null);
            }, remaining);
        });

        return () => {
            window.clearTimeout(showTimer);
            window.clearTimeout(hideTimer);
            offStart();
            offFinish();
        };
    }, [delay]);

    return pendingPath;
}
