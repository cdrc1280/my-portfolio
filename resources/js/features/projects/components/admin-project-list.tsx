import { Link, router } from '@inertiajs/react';
import { Pencil, Star, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { ConfirmDialog } from '@/components/shared/confirm-dialog';
import { SortableList } from '@/components/shared/sortable-list';
import { Button } from '@/components/ui/button';
import { ProjectStatusBadge } from '@/features/projects/components/project-status-badge';
import type { AdminProject } from '@/features/projects/types';
import { destroy, edit, reorder } from '@/routes/admin/projects';

/**
 * Sortable list of projects. Order changes are applied optimistically and
 * persisted immediately; the server's order wins once the request returns.
 */
export function AdminProjectList({ projects }: { projects: AdminProject[] }) {
    const [pendingOrder, setPendingOrder] = useState<number[] | null>(null);
    const [saving, setSaving] = useState(false);

    const items = pendingOrder
        ? pendingOrder.map((id) => projects.find((project) => project.id === id)).filter((p) => p !== undefined)
        : projects;

    const persistOrder = (ordered: AdminProject[]) => {
        const ids = ordered.map((project) => project.id);
        setPendingOrder(ids);
        setSaving(true);

        router.post(
            reorder.url(),
            { ids },
            {
                preserveScroll: true,
                onFinish: () => {
                    setSaving(false);
                    setPendingOrder(null);
                },
            },
        );
    };

    return (
        <SortableList
            items={items}
            onReorder={persistOrder}
            itemLabel={(project) => project.title}
            disabled={saving}
            renderItem={(project) => (
                <div className="flex flex-wrap items-center gap-x-4 gap-y-2">
                    <div className="min-w-0 flex-1">
                        <p className="flex items-center gap-2 font-medium">
                            <Link href={edit(project.id)} className="truncate hover:underline">
                                {project.title}
                            </Link>
                            {project.is_featured && (
                                <Star className="size-3.5 fill-current text-amber-500" aria-label="Featured" />
                            )}
                        </p>
                        <p className="truncate text-sm text-muted-foreground">
                            {project.tags.map((tag) => tag.name).join(', ') || 'No tags'}
                        </p>
                    </div>
                    <ProjectStatusBadge status={project.status} />
                    <div className="flex gap-1">
                        <Button variant="ghost" size="icon" asChild>
                            <Link href={edit(project.id)} aria-label={`Edit ${project.title}`}>
                                <Pencil aria-hidden />
                            </Link>
                        </Button>
                        <ConfirmDialog
                            title={`Delete "${project.title}"?`}
                            description="This permanently removes the project and its images."
                            confirmLabel="Delete"
                            onConfirm={() => router.delete(destroy.url(project.id), { preserveScroll: true })}
                            trigger={
                                <Button variant="ghost" size="icon" aria-label={`Delete ${project.title}`}>
                                    <Trash2 aria-hidden />
                                </Button>
                            }
                        />
                    </div>
                </div>
            )}
        />
    );
}
