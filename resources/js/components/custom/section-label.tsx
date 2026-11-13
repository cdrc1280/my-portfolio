import { cn } from '@/lib/utils';

/** Small mono label styled like a code comment: `// projects`. */
export function SectionLabel({ children, className }: { children: string; className?: string }) {
    return (
        <p className={cn('font-mono text-xs text-muted-foreground', className)}>
            <span aria-hidden className="text-primary">
                {'// '}
            </span>
            {children}
        </p>
    );
}
