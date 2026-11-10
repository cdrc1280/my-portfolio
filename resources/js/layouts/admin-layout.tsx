import AdminSidebarLayout from '@/layouts/app/app-sidebar-layout';
import type { AppLayoutProps } from '@/types';

/** Authenticated CMS shell: collapsible sidebar + breadcrumb header. */
export default function AdminLayout({ breadcrumbs = [], children }: AppLayoutProps) {
    return <AdminSidebarLayout breadcrumbs={breadcrumbs}>{children}</AdminSidebarLayout>;
}
