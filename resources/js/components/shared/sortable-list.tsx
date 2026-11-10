import type { DragEndEvent } from '@dnd-kit/core';
import { closestCenter, DndContext, KeyboardSensor, PointerSensor, useSensor, useSensors } from '@dnd-kit/core';
import {
    arrayMove,
    SortableContext,
    sortableKeyboardCoordinates,
    useSortable,
    verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { GripVertical } from 'lucide-react';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

type Identifiable = { id: number };

type SortableListProps<TItem extends Identifiable> = {
    items: TItem[];
    /** Called with the full new order after a drop. */
    onReorder: (items: TItem[]) => void;
    renderItem: (item: TItem) => ReactNode;
    /** Accessible name used for each drag handle, e.g. the item title. */
    itemLabel: (item: TItem) => string;
    disabled?: boolean;
};

/**
 * Drag-to-reorder list (pointer and keyboard: focus a handle, press Space,
 * move with arrow keys, Space again to drop).
 */
export function SortableList<TItem extends Identifiable>({
    items,
    onReorder,
    renderItem,
    itemLabel,
    disabled = false,
}: SortableListProps<TItem>) {
    const sensors = useSensors(
        useSensor(PointerSensor, { activationConstraint: { distance: 4 } }),
        useSensor(KeyboardSensor, { coordinateGetter: sortableKeyboardCoordinates }),
    );

    const handleDragEnd = ({ active, over }: DragEndEvent) => {
        if (!over || active.id === over.id) {
            return;
        }

        const from = items.findIndex((item) => item.id === active.id);
        const to = items.findIndex((item) => item.id === over.id);

        onReorder(arrayMove(items, from, to));
    };

    return (
        <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={handleDragEnd}>
            <SortableContext items={items.map((item) => item.id)} strategy={verticalListSortingStrategy}>
                <ul className="divide-y rounded-xl border">
                    {items.map((item) => (
                        <SortableRow key={item.id} id={item.id} label={itemLabel(item)} disabled={disabled}>
                            {renderItem(item)}
                        </SortableRow>
                    ))}
                </ul>
            </SortableContext>
        </DndContext>
    );
}

function SortableRow({
    id,
    label,
    disabled,
    children,
}: {
    id: number;
    label: string;
    disabled: boolean;
    children: ReactNode;
}) {
    const { attributes, listeners, setNodeRef, setActivatorNodeRef, transform, transition, isDragging } = useSortable({
        id,
        disabled,
    });

    return (
        <li
            ref={setNodeRef}
            style={{ transform: CSS.Transform.toString(transform), transition }}
            className={cn('flex items-center gap-3 bg-background px-3 py-3', isDragging && 'relative z-10 shadow-md')}
        >
            <button
                type="button"
                ref={setActivatorNodeRef}
                className="cursor-grab touch-none rounded p-1 text-muted-foreground hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-40"
                aria-label={`Reorder ${label}`}
                disabled={disabled}
                {...attributes}
                {...listeners}
            >
                <GripVertical className="size-4" aria-hidden />
            </button>
            <div className="min-w-0 flex-1">{children}</div>
        </li>
    );
}
