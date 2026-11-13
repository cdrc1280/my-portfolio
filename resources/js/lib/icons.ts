import type { LucideIcon } from 'lucide-react';
import {
    BookOpen,
    Boxes,
    Cloud,
    Code,
    Container,
    Database,
    FlaskConical,
    Gauge,
    GitBranch,
    Layers,
    LayoutDashboard,
    LifeBuoy,
    MessagesSquare,
    Monitor,
    Rocket,
    Search,
    Server,
    ShieldCheck,
    Sparkles,
    Terminal,
    Users,
    Workflow,
    Wrench,
    Zap,
} from 'lucide-react';

/**
 * Icons the CMS can assign to services, work approaches and tags, stored by their kebab-case name.
 * A fixed list keeps the bundle small (no dynamic import of every lucide icon); unknown names fall
 * back to `Code`.
 */
export const contentIcons = {
    'book-open': BookOpen,
    boxes: Boxes,
    cloud: Cloud,
    code: Code,
    container: Container,
    database: Database,
    'flask-conical': FlaskConical,
    gauge: Gauge,
    'git-branch': GitBranch,
    layers: Layers,
    'layout-dashboard': LayoutDashboard,
    'life-buoy': LifeBuoy,
    'messages-square': MessagesSquare,
    monitor: Monitor,
    rocket: Rocket,
    search: Search,
    server: Server,
    'shield-check': ShieldCheck,
    sparkles: Sparkles,
    terminal: Terminal,
    users: Users,
    workflow: Workflow,
    wrench: Wrench,
    zap: Zap,
} satisfies Record<string, LucideIcon>;

export type ContentIconName = keyof typeof contentIcons;

export const contentIconNames = Object.keys(contentIcons) as ContentIconName[];

export function resolveContentIcon(name: string | null | undefined): LucideIcon {
    return name && name in contentIcons ? contentIcons[name as ContentIconName] : Code;
}
