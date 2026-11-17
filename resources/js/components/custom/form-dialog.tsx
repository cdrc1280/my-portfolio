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
import {
    Drawer,
    DrawerClose,
    DrawerContent,
    DrawerDescription,
    DrawerFooter,
    DrawerHeader,
    DrawerTitle,
    DrawerTrigger,
} from '@/components/ui/drawer';
import { Spinner } from '@/components/ui/spinner';
import { useIsMobile } from '@/hooks/use-mobile';

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
 * A small CMS form (create / edit a single record): a centred dialog on desktop, a bottom drawer on phones,
 * where the fields scroll and the actions stay pinned within thumb reach.
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
    const handleSubmit = (event: FormEvent) => {
        event.preventDefault();
        onSubmit();
    };

    const isMobile = useIsMobile();

    if (isMobile) {
        return (
            <Drawer open={open} onOpenChange={onOpenChange}>
                {trigger && <DrawerTrigger asChild>{trigger}</DrawerTrigger>}
                <DrawerContent className="max-h-[92svh]">
                    <form onSubmit={handleSubmit} className="flex min-h-0 flex-1 flex-col" noValidate>
                        <DrawerHeader className="group-data-[vaul-drawer-direction=bottom]/drawer-content:text-left">
                            <DrawerTitle>{title}</DrawerTitle>
                            {description && <DrawerDescription>{description}</DrawerDescription>}
                        </DrawerHeader>
                        <div className="grid min-h-0 flex-1 gap-5 overflow-y-auto px-4 pb-2">{children}</div>
                        <DrawerFooter className="border-t">
                            <Button type="submit" disabled={processing}>
                                {processing && <Spinner />}
                                {submitLabel}
                            </Button>
                            <DrawerClose asChild>
                                <Button type="button" variant="outline">
                                    Cancel
                                </Button>
                            </DrawerClose>
                        </DrawerFooter>
                    </form>
                </DrawerContent>
            </Drawer>
        );
    }

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
