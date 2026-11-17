import { Link } from '@inertiajs/react';
import {
    BriefcaseBusiness,
    ExternalLink,
    FolderKanban,
    Handshake,
    Inbox,
    LayoutGrid,
    ListChecks,
    MessageSquareQuote,
    Newspaper,
    Settings2,
    SquareKanban,
    Tags,
    Wrench,
} from 'lucide-react';
import AppLogo from '@/components/custom/app-logo';
import { NavFooter } from '@/components/custom/nav-footer';
import { NavMain } from '@/components/custom/nav-main';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarTrigger,
} from '@/components/ui/sidebar';
import { home } from '@/routes';
import { dashboard } from '@/routes/admin';
import { index as boardIndex } from '@/routes/admin/board';
import { index as experienceIndex } from '@/routes/admin/experience';
import { index as messagesIndex } from '@/routes/admin/messages';
import { index as postsIndex } from '@/routes/admin/posts';
import { edit as profileEdit } from '@/routes/admin/profile';
import { index as projectsIndex } from '@/routes/admin/projects';
import { index as servicesIndex } from '@/routes/admin/services';
import { index as tagsIndex } from '@/routes/admin/tags';
import { index as testimonialsIndex } from '@/routes/admin/testimonials';
import { index as usesIndex } from '@/routes/admin/uses';
import { index as approachesIndex } from '@/routes/admin/work-approaches';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    { title: 'Board', href: boardIndex(), icon: SquareKanban },
    { title: 'Projects', href: projectsIndex(), icon: FolderKanban, matchPrefix: true },
    { title: 'Tags & skills', href: tagsIndex(), icon: Tags, matchPrefix: true },
    { title: 'Experience', href: experienceIndex(), icon: BriefcaseBusiness, matchPrefix: true },
    { title: 'Testimonials', href: testimonialsIndex(), icon: MessageSquareQuote, matchPrefix: true },
    { title: 'Services', href: servicesIndex(), icon: Handshake, matchPrefix: true },
    { title: 'How I work', href: approachesIndex(), icon: ListChecks, matchPrefix: true },
    { title: 'Blog', href: postsIndex(), icon: Newspaper, matchPrefix: true },
    { title: 'Uses', href: usesIndex(), icon: Wrench, matchPrefix: true },
    { title: 'Inbox', href: messagesIndex(), icon: Inbox, matchPrefix: true },
    { title: 'Settings', href: profileEdit(), icon: Settings2 },
];

const footerNavItems: NavItem[] = [{ title: 'View site', href: home(), icon: ExternalLink }];

export function AppSidebar() {
    // The inset variant has no edge of its own, and in dark mode its background matches the page.
    return (
        <Sidebar collapsible="icon" variant="inset" className="border-r border-sidebar-border">
            {/* Collapse toggle sits right of the name. When the sidebar is icon-only the logo is hidden, so the
                toggle keeps its place at the top of the icon column. */}
            <SidebarHeader className="flex-row items-center gap-0 group-data-[collapsible=icon]:justify-center">
                <SidebarMenu className="min-w-0 flex-1 group-data-[collapsible=icon]:hidden">
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
                <SidebarTrigger className="shrink-0 text-muted-foreground hover:text-foreground" />
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
            </SidebarFooter>
        </Sidebar>
    );
}
