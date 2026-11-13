/**
 * Custom analytics events. A no-op unless Plausible or Umami is loaded (see
 * resources/views/partials/analytics.blade.php), so it is always safe to call.
 */
export type AnalyticsEvent =
    'Contact brief sent' | 'Book a call' | 'Résumé download' | 'Project live site' | 'Assistant question';

type Props = Record<string, string>;

declare global {
    interface Window {
        plausible?: (event: string, options?: { props?: Props }) => void;
        umami?: { track: (event: string, data?: Props) => void };
    }
}

export function track(event: AnalyticsEvent, props?: Props): void {
    if (typeof window === 'undefined') {
        return;
    }

    try {
        if (window.plausible) {
            window.plausible(event, props ? { props } : undefined);
        } else if (window.umami) {
            window.umami.track(event, props);
        }
    } catch {
        // Analytics must never break the page.
    }
}
