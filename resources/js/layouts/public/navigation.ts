import { index as projectsIndex } from '@/routes/projects';
import type { NavItem } from '@/types';

/** Primary navigation of the public site, in display order. */
export const publicNavigation: NavItem[] = [{ title: 'Projects', href: projectsIndex() }];
