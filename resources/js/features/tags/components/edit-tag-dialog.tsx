import { useForm } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import { FormField } from '@/components/shared/form-field';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import type { AdminTag } from '@/features/tags/types';
import { update } from '@/routes/admin/tags';

export function EditTagDialog({ tag }: { tag: AdminTag }) {
    const [open, setOpen] = useState(false);
    const form = useForm({ name: tag.name, slug: tag.slug });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.submit(update(tag.id), { preserveScroll: true, onSuccess: () => setOpen(false) });
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant="ghost" size="icon" aria-label={`Edit ${tag.name}`}>
                    <Pencil aria-hidden />
                </Button>
            </DialogTrigger>
            <DialogContent>
                <form onSubmit={submit} className="grid gap-5">
                    <DialogHeader>
                        <DialogTitle>Edit tag</DialogTitle>
                        <DialogDescription>Renaming a tag updates it on every project.</DialogDescription>
                    </DialogHeader>
                    <FormField label="Name" htmlFor={`tag-${tag.id}-name`} error={form.errors.name}>
                        <Input
                            id={`tag-${tag.id}-name`}
                            value={form.data.name}
                            onChange={(event) => form.setData('name', event.target.value)}
                        />
                    </FormField>
                    <FormField label="Slug" htmlFor={`tag-${tag.id}-slug`} error={form.errors.slug}>
                        <Input
                            id={`tag-${tag.id}-slug`}
                            value={form.data.slug}
                            onChange={(event) => form.setData('slug', event.target.value)}
                        />
                    </FormField>
                    <DialogFooter>
                        <Button type="submit" disabled={form.processing}>
                            Save
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
