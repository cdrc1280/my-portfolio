import type { FormEvent, ReactNode } from 'react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
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

/** Dialog wrapping a small CMS form (create / edit a single record). */
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
    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        onSubmit();
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            {trigger && <DialogTrigger asChild>{trigger}</DialogTrigger>}
            <DialogContent className={size === 'lg' ? 'max-h-[90svh] overflow-y-auto sm:max-w-2xl' : undefined}>
                <form onSubmit={handleSubmit} className="grid gap-5" noValidate>
                    <DialogHeader>
                        <DialogTitle>{title}</DialogTitle>
                        {description && <DialogDescription>{description}</DialogDescription>}
                    </DialogHeader>
                    {children}
                    <DialogFooter>
                        <DialogClose asChild>
                            <Button type="button" variant="outline">
                                Cancel
                            </Button>
                        </DialogClose>
                        <Button type="submit" disabled={processing}>
                            {processing && <Spinner />}
                            {submitLabel}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}
