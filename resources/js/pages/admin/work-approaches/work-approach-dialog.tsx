import type { ReactNode } from 'react';
import { FormDialog } from '@/components/custom/form-dialog';
import { FormField } from '@/components/custom/form-field';
import { IconSelect } from '@/components/custom/icon-select';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { useDialogForm } from '@/hooks/use-dialog-form';
import { store, update } from '@/routes/admin/work-approaches';
import type { WorkApproach } from '@/types/domains/work-approach';

export function WorkApproachDialog({ approach, trigger }: { approach?: WorkApproach; trigger: ReactNode }) {
    const { open, setOpen, form, submit } = useDialogForm(
        {
            title: approach?.title ?? '',
            description: approach?.description ?? '',
            snippet: approach?.snippet ?? '',
            icon: approach?.icon ?? '',
        },
        { storeUrl: store.url(), updateUrl: approach ? update.url(approach.id) : undefined },
    );
    const { data, setData, errors } = form;

    return (
        <FormDialog
            open={open}
            onOpenChange={setOpen}
            trigger={trigger}
            title={approach ? 'Edit approach' : 'Add approach'}
            processing={form.processing}
            onSubmit={submit}
        >
            <FormField required label="Title" htmlFor="wa-title" error={errors.title}>
                <Input id="wa-title" value={data.title} onChange={(e) => setData('title', e.target.value)} />
            </FormField>
            <FormField
                required
                label="Description"
                htmlFor="wa-description"
                error={errors.description}
                hint="One or two lines."
            >
                <Textarea
                    id="wa-description"
                    rows={3}
                    value={data.description}
                    onChange={(e) => setData('description', e.target.value)}
                />
            </FormField>
            <FormField label="Code snippet" htmlFor="wa-snippet" error={errors.snippet} hint="Shown in mono.">
                <Input
                    id="wa-snippet"
                    className="font-mono text-sm"
                    value={data.snippet}
                    onChange={(e) => setData('snippet', e.target.value)}
                />
            </FormField>
            <FormField label="Icon" htmlFor="wa-icon" error={errors.icon}>
                <IconSelect id="wa-icon" value={data.icon} onChange={(value) => setData('icon', value)} />
            </FormField>
        </FormDialog>
    );
}
