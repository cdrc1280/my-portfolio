import { useForm } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { useState } from 'react';
import { FormDialog } from '@/components/shared/form-dialog';
import { FormField } from '@/components/shared/form-field';
import { Input } from '@/components/ui/input';
import type { SkillCategory } from '@/features/skills/types';
import { store, update } from '@/routes/admin/skill-categories';

type CategoryDialogProps = {
    category?: SkillCategory;
    trigger: ReactNode;
};

export function CategoryDialog({ category, trigger }: CategoryDialogProps) {
    const [open, setOpen] = useState(false);
    const form = useForm({ name: category?.name ?? '' });

    const submit = () =>
        form.submit(category ? update(category.id) : store(), {
            preserveScroll: true,
            onSuccess: () => {
                setOpen(false);
                if (!category) {
                    form.reset();
                }
            },
        });

    return (
        <FormDialog
            open={open}
            onOpenChange={setOpen}
            trigger={trigger}
            title={category ? 'Rename category' : 'New category'}
            processing={form.processing}
            onSubmit={submit}
        >
            <FormField label="Name" htmlFor="category-name" error={form.errors.name}>
                <Input
                    id="category-name"
                    value={form.data.name}
                    onChange={(event) => form.setData('name', event.target.value)}
                    placeholder="e.g. Backend"
                />
            </FormField>
        </FormDialog>
    );
}
