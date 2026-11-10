import { router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import type { ReactNode } from 'react';
import { ConfirmDialog } from '@/components/shared/confirm-dialog';
import { EmptyState } from '@/components/shared/empty-state';
import { SortableList } from '@/components/shared/sortable-list';
import { Button } from '@/components/ui/button';
import { useReorder } from '@/hooks/use-reorder';

type CareerSectionProps<TItem extends { id: number }> = {
    items: TItem[];
    reorderUrl: string;
    destroyUrl: (item: TItem) => string;
    noun: string;
    itemLabel: (item: TItem) => string;
    renderSummary: (item: TItem) => ReactNode;
    /** Renders a create (no item) or edit dialog around the given trigger. */
    renderDialog: (trigger: ReactNode, item?: TItem) => ReactNode;
};

/** Sortable admin list with add / edit / delete for one career-history type. */
export function CareerSection<TItem extends { id: number }>({
    items,
    reorderUrl,
    destroyUrl,
    noun,
    itemLabel,
    renderSummary,
    renderDialog,
}: CareerSectionProps<TItem>) {
    const { items: ordered, reorder, saving } = useReorder(items, reorderUrl);

    const addButton = renderDialog(
        <Button>
            <Plus aria-hidden /> Add {noun}
        </Button>,
    );

    return (
        <div className="space-y-4">
            <div className="flex justify-end">{addButton}</div>
            {ordered.length === 0 ? (
                <EmptyState title={`No ${noun} entries yet`} />
            ) : (
                <SortableList
                    items={ordered}
                    onReorder={reorder}
                    disabled={saving}
                    itemLabel={itemLabel}
                    renderItem={(item) => (
                        <div className="flex items-center gap-3">
                            <div className="min-w-0 flex-1">{renderSummary(item)}</div>
                            {renderDialog(
                                <Button variant="ghost" size="icon" aria-label={`Edit ${itemLabel(item)}`}>
                                    <Pencil aria-hidden />
                                </Button>,
                                item,
                            )}
                            <ConfirmDialog
                                title={`Delete "${itemLabel(item)}"?`}
                                description="This cannot be undone."
                                confirmLabel="Delete"
                                onConfirm={() => router.delete(destroyUrl(item), { preserveScroll: true })}
                                trigger={
                                    <Button variant="ghost" size="icon" aria-label={`Delete ${itemLabel(item)}`}>
                                        <Trash2 aria-hidden />
                                    </Button>
                                }
                            />
                        </div>
                    )}
                />
            )}
        </div>
    );
}
