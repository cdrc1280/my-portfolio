import type { ReactNode } from 'react';
import { FormDialog } from '@/components/custom/form-dialog';
import { FormField } from '@/components/custom/form-field';
import { IconSelect } from '@/components/custom/icon-select';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { useDialogForm } from '@/hooks/use-dialog-form';
import { store, update } from '@/routes/admin/services';
import type { Service } from '@/types/domains/services';

export function ServiceDialog({ service, trigger }: { service?: Service; trigger: ReactNode }) {
    const { open, setOpen, form, submit } = useDialogForm(
        { title: service?.title ?? '', description: service?.description ?? '', icon: service?.icon ?? '' },
        { storeUrl: store.url(), updateUrl: service ? update.url(service.id) : undefined },
    );
    const { data, setData, errors } = form;

    return (
        <FormDialog
            open={open}
            onOpenChange={setOpen}
            trigger={trigger}
            title={service ? 'Edit service' : 'Add service'}
            processing={form.processing}
            onSubmit={submit}
        >
            <FormField required label="Title" htmlFor="svc-title" error={errors.title}>
                <Input id="svc-title" value={data.title} onChange={(e) => setData('title', e.target.value)} />
            </FormField>
            <FormField required label="Description" htmlFor="svc-description" error={errors.description}>
                <Textarea
                    id="svc-description"
                    rows={4}
                    value={data.description}
                    onChange={(e) => setData('description', e.target.value)}
                />
            </FormField>
            <FormField label="Icon" htmlFor="svc-icon" error={errors.icon}>
                <IconSelect id="svc-icon" value={data.icon} onChange={(value) => setData('icon', value)} />
            </FormField>
        </FormDialog>
    );
}
