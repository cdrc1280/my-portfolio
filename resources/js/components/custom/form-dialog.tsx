import type { FormEvent, ReactNode } from 'react';
import {
    ResponsiveDialog,
    ResponsiveDialogBody,
    ResponsiveDialogClose,
    ResponsiveDialogContent,
    ResponsiveDialogDescription,
    ResponsiveDialogFooter,
    ResponsiveDialogHeader,
    ResponsiveDialogTitle,
    ResponsiveDialogTrigger,
    useResponsiveDialogFormClass,
} from '@/components/custom/responsive-dialog';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';

type FormDialogProps = {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    trigger?: ReactNode;
    title: string;
    description?: string;
    submitLabel?: string;
    processing?: boolean;
    onSubmit: () => void;
    /** Wider dialog for longer forms. */
    size?: 'default' | 'lg';
    children: ReactNode;
};

/**
 * A small CMS form (create / edit a single record): a centred dialog on desktop, a bottom drawer on phones
 * (see ResponsiveDialog).
 */
export function FormDialog({
    open,
    onOpenChange,
    trigger,
    title,
    description,
    submitLabel = 'Save',
    processing = false,
    onSubmit,
    size = 'default',
    children,
}: FormDialogProps) {
    return (
        <ResponsiveDialog open={open} onOpenChange={onOpenChange}>
            {trigger && <ResponsiveDialogTrigger>{trigger}</ResponsiveDialogTrigger>}
            <ResponsiveDialogContent
                className={size === 'lg' ? 'max-h-[90svh] overflow-y-auto sm:max-w-2xl' : undefined}
            >
                <FormBody
                    title={title}
                    description={description}
                    submitLabel={submitLabel}
                    processing={processing}
                    onSubmit={onSubmit}
                >
                    {children}
                </FormBody>
            </ResponsiveDialogContent>
        </ResponsiveDialog>
    );
}

/** Inside the content so the form class can follow the dialog/drawer switch. */
function FormBody({
    title,
    description,
    submitLabel,
    processing,
    onSubmit,
    children,
}: Pick<FormDialogProps, 'title' | 'description' | 'processing' | 'onSubmit' | 'children'> & { submitLabel: string }) {
    const formClass = useResponsiveDialogFormClass();

    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        onSubmit();
    };

    return (
        <form onSubmit={handleSubmit} className={formClass} noValidate>
            <ResponsiveDialogBody>
                <ResponsiveDialogHeader>
                    <ResponsiveDialogTitle>{title}</ResponsiveDialogTitle>
                    {description && <ResponsiveDialogDescription>{description}</ResponsiveDialogDescription>}
                </ResponsiveDialogHeader>
                {children}
            </ResponsiveDialogBody>
            <ResponsiveDialogFooter>
                <ResponsiveDialogClose>
                    <Button type="button" variant="outline">
                        Cancel
                    </Button>
                </ResponsiveDialogClose>
                <Button type="submit" disabled={processing}>
                    {processing && <Spinner />}
                    {submitLabel}
                </Button>
            </ResponsiveDialogFooter>
        </form>
    );
}
