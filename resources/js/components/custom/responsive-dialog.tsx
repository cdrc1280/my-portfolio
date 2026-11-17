import { createContext, useContext } from 'react';
import type { ComponentProps, ReactNode } from 'react';
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
import { useIsMobile } from '@/hooks/use-mobile';
import { cn } from '@/lib/utils';

/**
 * A modal that is a centred dialog from 768px up and a bottom drawer on phones. On phones the body
 * (header included) scrolls and the footer stays pinned as one compact row, so the open keyboard
 * leaves room for the field being typed in. Every CMS modal goes through this; FormDialog builds on it.
 */
const MobileContext = createContext(false);

type RootProps = {
    open?: boolean;
    onOpenChange?: (open: boolean) => void;
    children: ReactNode;
};

export function ResponsiveDialog({ open, onOpenChange, children }: RootProps) {
    const isMobile = useIsMobile();
    const Root = isMobile ? Drawer : Dialog;

    return (
        <MobileContext.Provider value={isMobile}>
            <Root open={open} onOpenChange={onOpenChange}>
                {children}
            </Root>
        </MobileContext.Provider>
    );
}

export function ResponsiveDialogTrigger({ children }: { children: ReactNode }) {
    const Trigger = useContext(MobileContext) ? DrawerTrigger : DialogTrigger;

    return <Trigger asChild>{children}</Trigger>;
}

export function ResponsiveDialogClose({ children }: { children: ReactNode }) {
    const Close = useContext(MobileContext) ? DrawerClose : DialogClose;

    return <Close asChild>{children}</Close>;
}

/** Desktop classes only (e.g. a wider dialog); the drawer always spans the screen. */
export function ResponsiveDialogContent({ className, children }: { className?: string; children: ReactNode }) {
    if (useContext(MobileContext)) {
        return <DrawerContent className="max-h-[92svh]">{children}</DrawerContent>;
    }

    return <DialogContent className={className}>{children}</DialogContent>;
}

/**
 * Scrollable middle of the modal; put the header inside it so it scrolls away on phones. A `<form>`
 * wrapping body and footer should use `responsiveDialogFormClass`.
 */
export function ResponsiveDialogBody({ className, children }: { className?: string; children: ReactNode }) {
    const isMobile = useContext(MobileContext);

    return (
        <div
            className={cn(
                'grid gap-5',
                isMobile && 'min-h-0 flex-1 overflow-y-auto overscroll-contain px-4 pt-3 pb-4',
                className,
            )}
        >
            {children}
        </div>
    );
}

/** On phones a form inside the drawer must flex so its body can scroll above the pinned footer. */
export function useResponsiveDialogFormClass(): string {
    return useContext(MobileContext) ? 'flex min-h-0 flex-1 flex-col' : 'grid gap-5';
}

export function ResponsiveDialogHeader({ className, children }: { className?: string; children: ReactNode }) {
    if (useContext(MobileContext)) {
        return (
            <DrawerHeader
                className={cn('p-0 group-data-[vaul-drawer-direction=bottom]/drawer-content:text-left', className)}
            >
                {children}
            </DrawerHeader>
        );
    }

    return <DialogHeader className={className}>{children}</DialogHeader>;
}

export function ResponsiveDialogTitle(props: ComponentProps<typeof DialogTitle>) {
    const Title = useContext(MobileContext) ? DrawerTitle : DialogTitle;

    return <Title {...props} />;
}

export function ResponsiveDialogDescription(props: ComponentProps<typeof DialogDescription>) {
    const Description = useContext(MobileContext) ? DrawerDescription : DialogDescription;

    return <Description {...props} />;
}

/** Actions: Cancel first, then the primary action. On phones they sit side by side in one pinned row. */
export function ResponsiveDialogFooter({ className, children }: { className?: string; children: ReactNode }) {
    if (useContext(MobileContext)) {
        return (
            <DrawerFooter
                className={cn(
                    'grid auto-cols-fr grid-flow-col gap-2 border-t px-4 py-3 pb-[max(0.75rem,env(safe-area-inset-bottom))]',
                    className,
                )}
            >
                {children}
            </DrawerFooter>
        );
    }

    return <DialogFooter className={className}>{children}</DialogFooter>;
}
