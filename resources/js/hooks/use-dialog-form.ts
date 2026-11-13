import type { FormDataType, Page } from '@inertiajs/core';
import { useForm } from '@inertiajs/react';
import { useState } from 'react';
import { succeeded } from '@/lib/form-result';

type DialogFormOptions = {
    storeUrl: string;
    /** Present when editing an existing record. */
    updateUrl?: string;
    /** Send as multipart (file uploads). Updates are then POSTed with `_method: put`. */
    multipart?: boolean;
};

/**
 * Open state + form state for a create/edit dialog in the CMS. Closes on success and resets the
 * form after a create, so the next "Add" starts empty.
 */
export function useDialogForm<TForm extends FormDataType<TForm>>(initial: TForm, options: DialogFormOptions) {
    const [open, setOpen] = useState(false);
    const form = useForm<TForm>(initial);
    const editing = options.updateUrl !== undefined;

    const submit = () => {
        const onSuccess = (page: Page) => {
            if (!succeeded(page)) {
                return;
            }

            setOpen(false);

            if (!editing) {
                form.reset();
            }
        };

        if (options.multipart) {
            form.transform((data) => (editing ? { ...data, _method: 'put' } : data));
            form.post(options.updateUrl ?? options.storeUrl, { forceFormData: true, preserveScroll: true, onSuccess });

            return;
        }

        if (options.updateUrl !== undefined) {
            form.put(options.updateUrl, { preserveScroll: true, onSuccess });
        } else {
            form.post(options.storeUrl, { preserveScroll: true, onSuccess });
        }
    };

    return { open, setOpen, form, submit, editing };
}
