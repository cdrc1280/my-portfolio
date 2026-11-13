import { AdminPageSkeleton } from '@/components/custom/admin-page-skeleton';
import { AppContent } from '@/components/custom/app-content';
import { AppShell } from '@/components/custom/app-shell';
import { AppSidebar } from '@/components/custom/app-sidebar';
import { AppSidebarHeader } from '@/components/custom/app-sidebar-header';
import { usePageTransition } from '@/hooks/use-page-transition';
import type { AppLayoutProps } from '@/types';

export default function AppSidebarLayout({ children, breadcrumbs = [] }: AppLayoutProps) {
    const pendingPath = usePageTransition();

    return (
        <AppShell variant="sidebar">
            <AppSidebar />
            <AppContent variant="sidebar" className="min-w-0 overflow-x-clip">
                <AppSidebarHeader breadcrumbs={breadcrumbs} />
                {pendingPath && <AdminPageSkeleton path={pendingPath} />}
                {/* Kept mounted (just hidden) so a cancelled visit doesn't lose unsaved form input. */}
                <div hidden={pendingPath !== null}>{children}</div>
            </AppContent>
        </AppShell>
    );
}
