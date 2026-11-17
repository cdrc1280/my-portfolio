import type { DragEndEvent, DragOverEvent, UniqueIdentifier } from '@dnd-kit/core';
import {
    closestCorners,
    DndContext,
    KeyboardSensor,
    PointerSensor,
    useDroppable,
    useSensor,
    useSensors,
} from '@dnd-kit/core';
import {
    arrayMove,
    SortableContext,
    sortableKeyboardCoordinates,
    useSortable,
    verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { router } from '@inertiajs/react';
import { CalendarDays, GripVertical, Pencil, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { ConfirmDialog } from '@/components/custom/confirm-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { formatDate } from '@/lib/format';
import { cn } from '@/lib/utils';
import { clearDone } from '@/routes/admin/board';
import { destroy, move } from '@/routes/admin/board/cards';
import type { SelectOption } from '@/types';
import type { BoardCard, BoardColumn, CardPriority, CardStatus } from '@/types/domains/board';
import { CardDialog } from './card-dialog';

type KanbanBoardProps = {
    columns: BoardColumn[];
    statuses: SelectOption<CardStatus>[];
    priorities: SelectOption<CardPriority>[];
};

const columnId = (status: CardStatus) => `column:${status}`;

/** Index of the column holding a card id, or of the column itself when dropped on its empty area. */
function findColumn(columns: BoardColumn[], id: UniqueIdentifier): number {
    return columns.findIndex((column) => columnId(column.status) === id || column.cards.some((card) => card.id === id));
}

/**
 * Four-column board with drag and drop between and within columns (pointer, or keyboard: focus a
 * handle, Space, arrow keys, Space). The drop is shown at once, then saved; the server's order wins.
 */
export function KanbanBoard({ columns, statuses, priorities }: KanbanBoardProps) {
    const [draft, setDraft] = useState<BoardColumn[] | null>(null);
    const [saving, setSaving] = useState(false);
    const shown = draft ?? columns;

    const sensors = useSensors(
        useSensor(PointerSensor, { activationConstraint: { distance: 4 } }),
        useSensor(KeyboardSensor, { coordinateGetter: sortableKeyboardCoordinates }),
    );

    // Crossing into another column moves the card there while dragging, so the list can open a gap.
    const handleDragOver = ({ active, over }: DragOverEvent) => {
        if (!over) {
            return;
        }

        const current = draft ?? columns;
        const from = findColumn(current, active.id);
        const to = findColumn(current, over.id);

        if (from < 0 || to < 0 || from === to) {
            return;
        }

        const card = current[from].cards.find((item) => item.id === active.id);

        if (!card) {
            return;
        }

        const target = current[to].cards;
        const overIndex = target.findIndex((item) => item.id === over.id);
        const insertAt = overIndex < 0 ? target.length : overIndex;

        setDraft(
            current.map((column, index) => {
                if (index === from) {
                    return { ...column, cards: column.cards.filter((item) => item.id !== card.id) };
                }

                if (index === to) {
                    return { ...column, cards: [...target.slice(0, insertAt), card, ...target.slice(insertAt)] };
                }

                return column;
            }),
        );
    };

    const handleDragEnd = ({ active, over }: DragEndEvent) => {
        const current = draft ?? columns;
        const to = over ? findColumn(current, over.id) : -1;
        const at = findColumn(current, active.id);

        if (!over || to < 0 || at !== to) {
            setDraft(null);

            return;
        }

        const cards = current[to].cards;
        const from = cards.findIndex((card) => card.id === active.id);
        const overIndex = cards.findIndex((card) => card.id === over.id);
        const ordered = overIndex < 0 || overIndex === from ? cards : arrayMove(cards, from, overIndex);

        const original = columns[findColumn(columns, active.id)];
        const unchanged =
            original.status === current[to].status &&
            original.cards.map((card) => card.id).join() === ordered.map((card) => card.id).join();

        if (unchanged) {
            setDraft(null);

            return;
        }

        setDraft(current.map((column, index) => (index === to ? { ...column, cards: ordered } : column)));
        setSaving(true);

        router.post(
            move.url(Number(active.id)),
            { status: current[to].status, ids: ordered.map((card) => card.id) },
            {
                preserveScroll: true,
                onFinish: () => {
                    setDraft(null);
                    setSaving(false);
                },
            },
        );
    };

    return (
        <DndContext
            sensors={sensors}
            collisionDetection={closestCorners}
            onDragOver={handleDragOver}
            onDragEnd={handleDragEnd}
            onDragCancel={() => setDraft(null)}
        >
            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                {shown.map((column) => (
                    <Column
                        key={column.status}
                        column={column}
                        statuses={statuses}
                        priorities={priorities}
                        disabled={saving}
                    />
                ))}
            </div>
        </DndContext>
    );
}

function Column({
    column,
    statuses,
    priorities,
    disabled,
}: {
    column: BoardColumn;
    statuses: SelectOption<CardStatus>[];
    priorities: SelectOption<CardPriority>[];
    disabled: boolean;
}) {
    const { setNodeRef, isOver } = useDroppable({ id: columnId(column.status) });

    return (
        <section aria-label={column.label} className="flex min-w-0 flex-col rounded-xl border bg-muted/40">
            <header className="flex items-center gap-2 px-3 pt-3 pb-2">
                <h2 className="text-sm font-semibold">{column.label}</h2>
                <span className="rounded-full bg-background px-2 text-xs text-muted-foreground tabular-nums">
                    {column.cards.length}
                </span>
                <div className="ml-auto flex items-center">
                    {column.status === 'done' && column.cards.length > 0 && (
                        <ConfirmDialog
                            title="Clear the Done column?"
                            description={`This deletes ${column.cards.length} done ${column.cards.length === 1 ? 'card' : 'cards'}. It cannot be undone.`}
                            confirmLabel="Clear"
                            onConfirm={() => router.delete(clearDone.url(), { preserveScroll: true })}
                            trigger={
                                <Button variant="ghost" size="sm" className="text-xs text-muted-foreground">
                                    Clear
                                </Button>
                            }
                        />
                    )}
                    <CardDialog
                        status={column.status}
                        statuses={statuses}
                        priorities={priorities}
                        trigger={
                            <Button
                                variant="ghost"
                                size="icon"
                                className="size-8"
                                aria-label={`Add a card to ${column.label}`}
                            >
                                <Plus aria-hidden />
                            </Button>
                        }
                    />
                </div>
            </header>
            <SortableContext items={column.cards.map((card) => card.id)} strategy={verticalListSortingStrategy}>
                <ul
                    ref={setNodeRef}
                    className={cn(
                        'flex min-h-24 flex-1 flex-col gap-2 rounded-b-xl p-2 transition-colors',
                        isOver && 'bg-primary/5',
                    )}
                >
                    {column.cards.length === 0 && (
                        <li className="flex flex-1 items-center justify-center rounded-lg border border-dashed p-4 text-xs text-muted-foreground">
                            Drop cards here
                        </li>
                    )}
                    {column.cards.map((card) => (
                        <CardItem
                            key={card.id}
                            card={card}
                            statuses={statuses}
                            priorities={priorities}
                            disabled={disabled}
                        />
                    ))}
                </ul>
            </SortableContext>
        </section>
    );
}

function CardItem({
    card,
    statuses,
    priorities,
    disabled,
}: {
    card: BoardCard;
    statuses: SelectOption<CardStatus>[];
    priorities: SelectOption<CardPriority>[];
    disabled: boolean;
}) {
    const { attributes, listeners, setNodeRef, setActivatorNodeRef, transform, transition, isDragging } = useSortable({
        id: card.id,
        disabled,
    });

    return (
        <li
            ref={setNodeRef}
            style={{ transform: CSS.Transform.toString(transform), transition }}
            className={cn(
                'group flex gap-2 rounded-lg border bg-background p-2.5 shadow-xs',
                isDragging && 'relative z-10 opacity-80 shadow-md',
                card.status === 'done' && 'text-muted-foreground',
            )}
        >
            <button
                type="button"
                ref={setActivatorNodeRef}
                className="mt-0.5 h-fit cursor-grab touch-none rounded p-0.5 text-muted-foreground hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-40"
                aria-label={`Move ${card.title}`}
                disabled={disabled}
                {...attributes}
                {...listeners}
            >
                <GripVertical className="size-4" aria-hidden />
            </button>
            <div className="min-w-0 flex-1 space-y-1.5">
                <p className={cn('text-sm font-medium break-words', card.status === 'done' && 'line-through')}>
                    {card.title}
                </p>
                {card.notes && (
                    <p className="line-clamp-3 text-xs whitespace-pre-line text-muted-foreground">{card.notes}</p>
                )}
                {(card.priority !== 'normal' || card.due_on) && (
                    <div className="flex flex-wrap items-center gap-1.5">
                        {card.priority === 'high' && <Badge>{card.priority_label}</Badge>}
                        {card.priority === 'low' && <Badge variant="outline">{card.priority_label}</Badge>}
                        {card.due_on && (
                            <span
                                className={cn(
                                    'inline-flex items-center gap-1 text-xs text-muted-foreground',
                                    card.due_today && 'font-medium text-foreground',
                                    card.overdue && 'font-medium text-destructive',
                                )}
                            >
                                <CalendarDays className="size-3.5" aria-hidden />
                                {formatDate(`${card.due_on}T00:00:00`)}
                                {card.overdue && ' · overdue'}
                                {card.due_today && ' · today'}
                            </span>
                        )}
                    </div>
                )}
            </div>
            <div className="flex flex-col">
                <CardDialog
                    card={card}
                    statuses={statuses}
                    priorities={priorities}
                    trigger={
                        <Button variant="ghost" size="icon" className="size-7" aria-label={`Edit ${card.title}`}>
                            <Pencil aria-hidden />
                        </Button>
                    }
                />
                <ConfirmDialog
                    title={`Delete "${card.title}"?`}
                    description="This cannot be undone."
                    confirmLabel="Delete"
                    onConfirm={() => router.delete(destroy.url(card.id), { preserveScroll: true })}
                    trigger={
                        <Button variant="ghost" size="icon" className="size-7" aria-label={`Delete ${card.title}`}>
                            <Trash2 aria-hidden />
                        </Button>
                    }
                />
            </div>
        </li>
    );
}
