import type { ReactNode } from 'react';
import { FormDialog } from '@/components/custom/form-dialog';
import { FormField } from '@/components/custom/form-field';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useDialogForm } from '@/hooks/use-dialog-form';
import { store, update } from '@/routes/admin/board/cards';
import type { SelectOption } from '@/types';
import type { BoardCard, CardPriority, CardStatus } from '@/types/domains/board';

type CardDialogProps = {
    card?: BoardCard;
    /** Column a new card starts in. */
    status?: CardStatus;
    statuses: SelectOption<CardStatus>[];
    priorities: SelectOption<CardPriority>[];
    trigger: ReactNode;
};

/** Create or edit a board card. The column select also moves a card without dragging. */
export function CardDialog({ card, status = 'todo', statuses, priorities, trigger }: CardDialogProps) {
    const { open, setOpen, form, submit } = useDialogForm(
        {
            title: card?.title ?? '',
            notes: card?.notes ?? '',
            status: card?.status ?? status,
            priority: card?.priority ?? 'normal',
            due_on: card?.due_on ?? '',
        },
        { storeUrl: store.url(), updateUrl: card ? update.url(card.id) : undefined },
    );
    const { data, setData, errors } = form;
    const prefix = card ? `card-${card.id}` : `card-new-${status}`;

    return (
        <FormDialog
            open={open}
            onOpenChange={setOpen}
            trigger={trigger}
            title={card ? 'Edit card' : 'Add card'}
            description="Private to you. Nothing on the board is shown on the site."
            processing={form.processing}
            onSubmit={submit}
        >
            <FormField required label="Title" htmlFor={`${prefix}-title`} error={errors.title}>
                <Input id={`${prefix}-title`} value={data.title} onChange={(e) => setData('title', e.target.value)} />
            </FormField>
            <FormField label="Notes" htmlFor={`${prefix}-notes`} error={errors.notes}>
                <Textarea
                    id={`${prefix}-notes`}
                    rows={5}
                    value={data.notes}
                    onChange={(e) => setData('notes', e.target.value)}
                />
            </FormField>
            <div className="grid gap-4 sm:grid-cols-3">
                <FormField required label="Column" htmlFor={`${prefix}-status`} error={errors.status}>
                    <Select value={data.status} onValueChange={(value) => setData('status', value as CardStatus)}>
                        <SelectTrigger id={`${prefix}-status`} className="w-full">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            {statuses.map((option) => (
                                <SelectItem key={option.value} value={option.value}>
                                    {option.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </FormField>
                <FormField required label="Priority" htmlFor={`${prefix}-priority`} error={errors.priority}>
                    <Select value={data.priority} onValueChange={(value) => setData('priority', value as CardPriority)}>
                        <SelectTrigger id={`${prefix}-priority`} className="w-full">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            {priorities.map((option) => (
                                <SelectItem key={option.value} value={option.value}>
                                    {option.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </FormField>
                <FormField label="Due date" htmlFor={`${prefix}-due`} error={errors.due_on}>
                    <Input
                        id={`${prefix}-due`}
                        type="date"
                        value={data.due_on}
                        onChange={(e) => setData('due_on', e.target.value)}
                    />
                </FormField>
            </div>
        </FormDialog>
    );
}
