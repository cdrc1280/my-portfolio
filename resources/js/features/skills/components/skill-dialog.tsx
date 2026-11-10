import { useForm } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { useState } from 'react';
import { FormDialog } from '@/components/shared/form-dialog';
import { FormField } from '@/components/shared/form-field';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Skill, SkillCategory } from '@/features/skills/types';
import { store, update } from '@/routes/admin/skills';

type SkillDialogProps = {
    categories: SkillCategory[];
    categoryId: number;
    skill?: Skill;
    trigger: ReactNode;
};

export function SkillDialog({ categories, categoryId, skill, trigger }: SkillDialogProps) {
    const [open, setOpen] = useState(false);
    const form = useForm({ name: skill?.name ?? '', skill_category_id: categoryId });

    const submit = () =>
        form.submit(skill ? update(skill.id) : store(), {
            preserveScroll: true,
            onSuccess: () => {
                setOpen(false);
                if (!skill) {
                    form.reset('name');
                }
            },
        });

    return (
        <FormDialog
            open={open}
            onOpenChange={setOpen}
            trigger={trigger}
            title={skill ? 'Edit skill' : 'Add skill'}
            processing={form.processing}
            onSubmit={submit}
        >
            <FormField label="Name" htmlFor="skill-name" error={form.errors.name}>
                <Input
                    id="skill-name"
                    value={form.data.name}
                    onChange={(event) => form.setData('name', event.target.value)}
                    placeholder="e.g. Laravel"
                />
            </FormField>
            <FormField label="Category" htmlFor="skill-category" error={form.errors.skill_category_id}>
                <Select
                    value={String(form.data.skill_category_id)}
                    onValueChange={(value) => form.setData('skill_category_id', Number(value))}
                >
                    <SelectTrigger id="skill-category">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        {categories.map((category) => (
                            <SelectItem key={category.id} value={String(category.id)}>
                                {category.name}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </FormField>
        </FormDialog>
    );
}
