import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

type PageHeaderProps = {
    title: string;
    description?: ReactNode;
    actions?: ReactNode;
    /** Public pages use a larger, airier heading than the CMS. */
    size?: 'default' | 'lg';
    className?: string;
};

export function PageHeader({ title, description, actions, size = 'default', className }: PageHeaderProps) {
    return (
        <header className={cn('flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between', className)}>
            <div className="space-y-1.5">
                <h1
                    className={cn(
                        'font-semibold tracking-tight text-balance',
                        size === 'lg' ? 'text-3xl sm:text-4xl' : 'text-2xl',
                    )}
                >
                    {title}
                </h1>
                {description && (
                    <p className={cn('max-w-2xl text-muted-foreground', size === 'lg' && 'text-lg')}>{description}</p>
                )}
            </div>
            {actions && <div className="flex shrink-0 items-center gap-2">{actions}</div>}
        </header>
    );
}
