import { router } from '@inertiajs/react';
import { useEffect } from 'react';
import { toast } from 'sonner';

const MESSAGES: Record<number, string> = {
    413: 'That upload is too large. Please choose a smaller file.',
    419: 'Your session expired. Please refresh the page and try again.',
    429: 'Too many attempts. Please wait a minute and try again.',
};

function messageFor(status: number): string {
    return (
        MESSAGES[status] ??
        (status >= 500
            ? 'Something went wrong on my side. Please try again.'
            : 'Something went wrong. Please try again.')
    );
}

/**
 * Client-side safety net. The server already turns errors into toasts or the themed error page
 * (FriendlyErrorResponse); this catches whatever still arrives as a non-Inertia response, or no
 * response at all, so Inertia never shows its blank error dialog. In local development, 5xx keeps
 * Inertia's dialog because it contains the Laravel debug page.
 */
export function useHttpErrorFallback(): void {
    useEffect(() => {
        const stopHttp = router.on('httpException', (event) => {
            const status = event.detail.response.status;

            if (import.meta.env.DEV && status >= 500) {
                return;
            }

            event.preventDefault();
            toast.error(messageFor(status));
        });

        const stopNetwork = router.on('networkError', (event) => {
            event.preventDefault();
            toast.error("Couldn't reach the server. Check your connection and try again.");
        });

        return () => {
            stopHttp();
            stopNetwork();
        };
    }, []);
}
