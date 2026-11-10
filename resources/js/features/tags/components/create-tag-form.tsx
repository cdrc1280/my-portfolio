import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store } from '@/routes/admin/tags';

export function CreateTagForm() {
    const form = useForm({ name: '' });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.submit(store(), { preserveScroll: true, onSuccess: () => form.reset() });
    };

    return (
        <form onSubmit={submit} className="flex flex-col gap-2 sm:flex-row sm:items-start">
            <div className="grid flex-1 gap-1">
                <Label htmlFor="new-tag" className="sr-only">
                    New tag name
                </Label>
                <Input
                    id="new-tag"
                    placeholder="New tag, e.g. Laravel"
                    value={form.data.name}
                    onChange={(event) => form.setData('name', event.target.value)}
                />
                <InputError message={form.errors.name} />
            </div>
            <Button type="submit" disabled={form.processing || form.data.name.trim() === ''}>
                Add tag
            </Button>
        </form>
    );
}
