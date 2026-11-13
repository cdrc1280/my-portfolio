import type { ReactNode } from 'react';
import { FormDialog } from '@/components/custom/form-dialog';
import { FormField } from '@/components/custom/form-field';
import { IconSelect } from '@/components/custom/icon-select';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useDialogForm } from '@/hooks/use-dialog-form';
import { store, update } from '@/routes/admin/tags';
import type { SelectOption, TagCategory } from '@/types';
import type { AdminTag } from '@/types/domains/tags';

const NOT_A_SKILL = '__none';

type TagDialogProps = {
    tag?: AdminTag;
    categories: SelectOption<TagCategory>[];
    trigger: ReactNode;
};

/** Create or edit a tag. A category also lists it in the public skills section. */
export function TagDialog({ tag, categories, trigger }: TagDialogProps) {
    const { open, setOpen, form, submit } = useDialogForm(
        { name: tag?.name ?? '', slug: tag?.slug ?? '', category: tag?.category ?? '', icon: tag?.icon ?? '' },
        { storeUrl: store.url(), updateUrl: tag ? update.url(tag.id) : undefined },
    );
    const { data, setData, errors } = form;
    const prefix = tag ? `tag-${tag.id}` : 'tag-new';

    return (
        <FormDialog
            open={open}
            onOpenChange={setOpen}
            trigger={trigger}
            title={tag ? 'Edit tag' : 'Add tag'}
            description="Renaming a tag updates it on every project and post."
            processing={form.processing}
            onSubmit={submit}
        >
            <FormField required label="Name" htmlFor={`${prefix}-name`} error={errors.name}>
                <Input id={`${prefix}-name`} value={data.name} onChange={(e) => setData('name', e.target.value)} />
            </FormField>
            <FormField label="Slug" htmlFor={`${prefix}-slug`} error={errors.slug} hint="Leave empty to derive it.">
                <Input id={`${prefix}-slug`} value={data.slug} onChange={(e) => setData('slug', e.target.value)} />
            </FormField>
            <FormField
                label="Skills category"
                htmlFor={`${prefix}-category`}
                error={errors.category}
                hint="Shown in the skills section under this group. “Not a skill” keeps it as a badge only."
            >
                <Select
                    value={data.category === '' ? NOT_A_SKILL : data.category}
                    onValueChange={(value) => setData('category', value === NOT_A_SKILL ? '' : value)}
                >
                    <SelectTrigger id={`${prefix}-category`} className="w-full">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value={NOT_A_SKILL}>Not a skill</SelectItem>
                        {categories.map((category) => (
                            <SelectItem key={category.value} value={category.value}>
                                {category.label}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </FormField>
            <FormField label="Icon" htmlFor={`${prefix}-icon`} error={errors.icon}>
                <IconSelect id={`${prefix}-icon`} value={data.icon} onChange={(value) => setData('icon', value)} />
            </FormField>
        </FormDialog>
    );
}
