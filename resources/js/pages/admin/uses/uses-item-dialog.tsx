import type { ReactNode } from 'react';
import { FormDialog } from '@/components/custom/form-dialog';
import { FormField } from '@/components/custom/form-field';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { useDialogForm } from '@/hooks/use-dialog-form';
import { store, update } from '@/routes/admin/uses';
import type { UsesItem } from '@/types/domains/uses';

export function UsesItemDialog({
    item,
    categories,
    trigger,
}: {
    item?: UsesItem;
    categories: string[];
    trigger: ReactNode;
}) {
    const { open, setOpen, form, submit } = useDialogForm(
        {
            category: item?.category ?? '',
            name: item?.name ?? '',
            description: item?.description ?? '',
            url: item?.url ?? '',
        },
        { storeUrl: store.url(), updateUrl: item ? update.url(item.id) : undefined },
    );
    const { data, setData, errors } = form;
    const listId = item ? `uses-categories-${item.id}` : 'uses-categories-new';

    return (
        <FormDialog
            open={open}
            onOpenChange={setOpen}
            trigger={trigger}
            title={item ? 'Edit item' : 'Add item'}
            processing={form.processing}
            onSubmit={submit}
        >
            <FormField
                required
                label="Category"
                htmlFor="uses-category"
                error={errors.category}
                hint="e.g. Editor, Terminal, Hardware"
            >
                <Input
                    id="uses-category"
                    list={listId}
                    value={data.category}
                    onChange={(e) => setData('category', e.target.value)}
                />
                <datalist id={listId}>
                    {categories.map((category) => (
                        <option key={category} value={category} />
                    ))}
                </datalist>
            </FormField>
            <FormField required label="Name" htmlFor="uses-name" error={errors.name}>
                <Input id="uses-name" value={data.name} onChange={(e) => setData('name', e.target.value)} />
            </FormField>
            <FormField label="Description" htmlFor="uses-description" error={errors.description}>
                <Textarea
                    id="uses-description"
                    rows={2}
                    value={data.description}
                    onChange={(e) => setData('description', e.target.value)}
                />
            </FormField>
            <FormField label="URL" htmlFor="uses-url" error={errors.url}>
                <Input id="uses-url" type="url" value={data.url} onChange={(e) => setData('url', e.target.value)} />
            </FormField>
        </FormDialog>
    );
}
