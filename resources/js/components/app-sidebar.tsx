import { Link } from '@inertiajs/react';
import { BriefcaseBusiness, ExternalLink, FolderKanban, LayoutGrid, Sparkles, Tags } from 'lucide-react';
import AppLogo from '@/components/app-logo';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { home } from '@/routes';
import { dashboard } from '@/routes/admin';
import { index as experienceIndex } from '@/routes/admin/experience';
import { index as projectsIndex } from '@/routes/admin/projects';
import { index as skillsIndex } from '@/routes/admin/skills';
import { index as tagsIndex } from '@/routes/admin/tags';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    { title: 'Projects', href: projectsIndex(), icon: FolderKanban, matchPrefix: true },
    { title: 'Tags', href: tagsIndex(), icon: Tags, matchPrefix: true },
    { title: 'Skills', href: skillsIndex(), icon: Sparkles, matchPrefix: true },
    { title: 'Experience', href: experienceIndex(), icon: BriefcaseBusiness, matchPrefix: true },
];

const footerNavItems: NavItem[] = [{ title: 'View site', href: home(), icon: ExternalLink }];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
