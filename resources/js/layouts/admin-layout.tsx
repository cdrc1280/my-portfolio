import { lazy, Suspense } from 'react';
import type { AppLayoutProps } from '@/types';

// The sidebar shell (sidebar, menus, notification drawer) loads on demand, so public visitors never
// download it. It is a stable lazy component, so the persistent layout keeps its state across visits.
const AdminSidebarLayout = lazy(() => import('@/layouts/app/app-sidebar-layout'));

/** Authenticated CMS shell: collapsible sidebar + breadcrumb header. */
export default function AdminLayout({ breadcrumbs = [], children }: AppLayoutProps) {
    return (
        // Shown only on the first admin visit while the shell's code loads.
        <Suspense fallback={<div className="min-h-svh bg-background" />}>
            <AdminSidebarLayout breadcrumbs={breadcrumbs}>{children}</AdminSidebarLayout>
        </Suspense>
    );
}
