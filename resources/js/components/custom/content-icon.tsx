import type { LucideProps } from 'lucide-react';
import { resolveContentIcon } from '@/lib/icons';

/** Renders a CMS-chosen icon by name (decorative). */
export function ContentIcon({ icon, ...props }: { icon: string | null | undefined } & LucideProps) {
    const Icon = resolveContentIcon(icon);

    return <Icon aria-hidden {...props} />;
}
