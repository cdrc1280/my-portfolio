import { about } from '@/routes';
import { index as blogIndex } from '@/routes/blog';
import { show as contact } from '@/routes/contact';
import { index as projectsIndex } from '@/routes/projects';
import type { NavItem } from '@/types';

/** Primary navigation of the public site, in display order. */
export const publicNavigation: NavItem[] = [
    { title: 'Projects', href: projectsIndex() },
    { title: 'About', href: about() },
    { title: 'Blog', href: blogIndex() },
    { title: 'Contact', href: contact() },
];
