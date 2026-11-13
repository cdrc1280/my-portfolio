import type { Page } from '@inertiajs/core';
import type { FlashToast } from '@/types/ui';

/**
 * Whether a submit really succeeded. Inertia calls `onSuccess` for any redirect without validation
 * errors, including the server's fallback for a rate limit, expired session or server error, which
 * redirects back with an error toast (FriendlyErrorResponse). Forms check this before resetting their
 * fields or closing a dialog, so the visitor never loses what they typed.
 */
export function succeeded(page: Page): boolean {
    const toast = (page.flash as { toast?: FlashToast } | undefined)?.toast;

    return toast?.type !== 'error';
}
