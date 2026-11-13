import { about, services, uses } from '@/routes';
import { index as blogIndex } from '@/routes/blog';
import { show as contact } from '@/routes/contact';
import { index as projectsIndex } from '@/routes/projects';
import type { NavItem } from '@/types';

type Visibility = { blog: boolean; services: boolean; uses: boolean };

/** Primary navigation of the public site, in display order. Pages without content are left out. */
export function publicNavigation(visible: Visibility): NavItem[] {
    return [
        { title: 'Projects', href: projectsIndex() },
        ...(visible.services ? [{ title: 'Services', href: services() }] : []),
        { title: 'About', href: about() },
        ...(visible.blog ? [{ title: 'Blog', href: blogIndex() }] : []),
        { title: 'Contact', href: contact() },
    ];
}

/** Secondary links shown in the footer. */
export function footerNavigation(visible: Visibility): NavItem[] {
    return visible.uses ? [{ title: 'Uses', href: uses() }] : [];
}
