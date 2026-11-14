import { Breadcrumbs } from '@/components/custom/breadcrumbs';
import { NavUser } from '@/components/custom/nav-user';
import { NotificationBell } from '@/components/custom/notification-bell';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItem as BreadcrumbItemType } from '@/types';

export function AppSidebarHeader({ breadcrumbs = [] }: { breadcrumbs?: BreadcrumbItemType[] }) {
    return (
        <header className="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/50 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4">
            <div className="flex items-center gap-2">
                {/* On desktop the toggle lives in the sidebar header; on mobile the sidebar is off-canvas, so open it from here. */}
                <SidebarTrigger className="-ml-1 md:hidden" />
                <Breadcrumbs breadcrumbs={breadcrumbs} />
            </div>
            <div className="ml-auto flex items-center gap-1">
                <NotificationBell />
                <NavUser />
            </div>
        </header>
    );
}
