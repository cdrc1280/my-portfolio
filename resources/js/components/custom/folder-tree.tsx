import { cn } from '@/lib/utils';

/** A file or folder. A node with `children` (even an empty array) renders as a folder. */
export type FolderNode = {
    name: string;
    /** Short explanation shown as a trailing comment. */
    note?: string;
    children?: FolderNode[];
};

/**
 * Renders a project's architecture as a mono folder tree (`├──` / `└──`), with optional
 * comment-style notes. Exposed to assistive tech as a nested list.
 */
export function FolderTree({ nodes, label, className }: { nodes: FolderNode[]; label: string; className?: string }) {
    return (
        // A focusable region so keyboard users can scroll it horizontally on narrow screens.
        <div
            role="region"
            aria-label={label}
            tabIndex={0}
            className={cn(
                'overflow-x-auto rounded-lg border border-border bg-card p-4 font-mono text-[13px] leading-6',
                className,
            )}
        >
            <ul className="min-w-max">
                <Branch nodes={nodes} prefix="" />
            </ul>
        </div>
    );
}

function Branch({ nodes, prefix }: { nodes: FolderNode[]; prefix: string }) {
    return nodes.map((node, index) => {
        const last = index === nodes.length - 1;
        const folder = node.children !== undefined;

        return (
            <li key={`${prefix}${node.name}`}>
                <span aria-hidden className="whitespace-pre text-border-strong">
                    {prefix}
                    {last ? '└── ' : '├── '}
                </span>
                <span className={folder ? 'text-foreground' : 'text-muted-foreground'}>
                    {node.name}
                    {folder && '/'}
                </span>
                {node.note && <span className="text-primary">{`  # ${node.note}`}</span>}
                {folder && node.children && node.children.length > 0 && (
                    <ul>
                        <Branch nodes={node.children} prefix={`${prefix}${last ? '    ' : '│   '}`} />
                    </ul>
                )}
            </li>
        );
    });
}
