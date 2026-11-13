import { usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { AssistantWidget } from '@/components/custom/assistant/assistant-widget';
import { DotFieldBackground } from '@/components/custom/dot-field-background';
import { PageSkeleton } from '@/components/custom/page-skeleton';
import { SiteFooter } from '@/components/custom/site-footer';
import { SiteHeader } from '@/components/custom/site-header';
import { usePageTransition } from '@/hooks/use-page-transition';

/**
 * Public shell for the "Engineer's case study" theme: sticky header, content, footer, and the
 * interactive dot-field backdrop. Follows the dark (default) / light toggle. Body copy is muted;
 * headings opt into `text-foreground`.
 */
export default function PublicLayout({ children }: { children: ReactNode }) {
    const { site } = usePage().props;
    const pendingPath = usePageTransition();

    return (
        <div className="relative isolate flex min-h-svh flex-col bg-background font-sans text-muted-foreground antialiased">
            <DotFieldBackground />

            <a
                href="#main"
                className="sr-only rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[80]"
            >
                Skip to content
            </a>

            <SiteHeader name={site.name} />

            <main id="main" className="flex-1">
                {pendingPath && <PageSkeleton path={pendingPath} />}
                {/* Kept mounted (just hidden) so a cancelled visit doesn't lose state such as form input. */}
                <div hidden={pendingPath !== null}>{children}</div>
            </main>

            <SiteFooter name={site.name} />

            {site.assistant_enabled && <AssistantWidget />}
        </div>
    );
}
