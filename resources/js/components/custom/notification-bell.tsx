import { Link, router, usePage } from '@inertiajs/react';
import { Bell, CheckCheck, Inbox } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import {
    Drawer,
    DrawerContent,
    DrawerDescription,
    DrawerHeader,
    DrawerTitle,
    DrawerTrigger,
} from '@/components/ui/drawer';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { useInitials } from '@/hooks/use-initials';
import { useIsMobile } from '@/hooks/use-mobile';
import { cn } from '@/lib/utils';
import { index as inboxIndex, readAll, show } from '@/routes/admin/messages';
import type { ContactNotification, ContactNotifications } from '@/types/domains/contact';

/**
 * Admin header bell for unread briefs (shared `notifications` prop). A dropdown on desktop and a
 * bottom drawer on mobile, with the same list, "Mark all as read" and a link to the inbox.
 */
export function NotificationBell() {
    const { notifications } = usePage().props;
    const isMobile = useIsMobile();
    const [open, setOpen] = useState(false);

    if (!notifications) {
        return null;
    }

    const count = notifications.unread_count;
    const trigger = (
        <Button
            variant="ghost"
            size="icon"
            className="relative size-9"
            aria-label={count > 0 ? `Notifications, ${count} unread` : 'Notifications'}
        >
            <Bell className="size-[18px]" aria-hidden />
            {count > 0 && (
                <span className="absolute top-1 right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-emerald-500 px-1 text-[10px] leading-none font-semibold text-white tabular-nums ring-2 ring-background">
                    {count > 9 ? '9+' : count}
                </span>
            )}
        </Button>
    );
    const close = () => setOpen(false);

    if (isMobile) {
        return (
            <Drawer open={open} onOpenChange={setOpen}>
                <DrawerTrigger asChild>{trigger}</DrawerTrigger>
                <DrawerContent className="max-h-[85svh]">
                    <DrawerHeader className="flex-row items-center justify-between gap-3 border-b">
                        <div className="text-left">
                            <DrawerTitle>Notifications</DrawerTitle>
                            <DrawerDescription>{summary(count)}</DrawerDescription>
                        </div>
                        <MarkAllButton count={count} onDone={close} />
                    </DrawerHeader>
                    <NotificationList notifications={notifications} onNavigate={close} className="overflow-y-auto" />
                    <div className="border-t p-3 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
                        <Button asChild variant="outline" className="w-full">
                            <Link href={inboxIndex()} onClick={close}>
                                <Inbox aria-hidden />
                                Open inbox
                            </Link>
                        </Button>
                    </div>
                </DrawerContent>
            </Drawer>
        );
    }

    return (
        <DropdownMenu open={open} onOpenChange={setOpen}>
            <DropdownMenuTrigger asChild>{trigger}</DropdownMenuTrigger>
            <DropdownMenuContent
                align="end"
                sideOffset={8}
                className="w-[min(24rem,calc(100vw-2rem))] overflow-hidden rounded-xl p-0"
            >
                <div className="flex items-center justify-between gap-3 border-b px-4 py-3">
                    <div>
                        <p className="text-sm font-medium">Notifications</p>
                        <p className="text-xs text-muted-foreground">{summary(count)}</p>
                    </div>
                    <MarkAllButton count={count} onDone={close} />
                </div>
                <NotificationList
                    notifications={notifications}
                    onNavigate={close}
                    className="max-h-96 overflow-y-auto"
                />
                <div className="border-t p-2">
                    <Button asChild variant="ghost" size="sm" className="w-full">
                        <Link href={inboxIndex()} onClick={close}>
                            <Inbox aria-hidden />
                            Open inbox
                        </Link>
                    </Button>
                </div>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}

function summary(count: number): string {
    if (count === 0) {
        return 'No unread messages';
    }

    return count === 1 ? '1 unread message' : `${count} unread messages`;
}

function MarkAllButton({ count, onDone }: { count: number; onDone: () => void }) {
    const [processing, setProcessing] = useState(false);

    if (count === 0) {
        return null;
    }

    return (
        <Button
            variant="ghost"
            size="sm"
            className="h-8 shrink-0 text-xs text-muted-foreground hover:text-foreground"
            disabled={processing}
            onClick={() =>
                router.visit(readAll(), {
                    preserveScroll: true,
                    onStart: () => setProcessing(true),
                    onFinish: () => setProcessing(false),
                    onSuccess: onDone,
                })
            }
        >
            <CheckCheck aria-hidden />
            Mark all as read
        </Button>
    );
}

type NotificationListProps = {
    notifications: ContactNotifications;
    onNavigate: () => void;
    className?: string;
};

function NotificationList({ notifications, onNavigate, className }: NotificationListProps) {
    if (notifications.items.length === 0) {
        return (
            <div className="flex flex-col items-center px-6 py-10 text-center">
                <span className="mb-3 flex size-10 items-center justify-center rounded-full bg-muted">
                    <Bell className="size-5 text-muted-foreground" aria-hidden />
                </span>
                <p className="text-sm font-medium">You're all caught up</p>
                <p className="mt-1 text-xs text-muted-foreground">New project briefs will show up here.</p>
            </div>
        );
    }

    return (
        <ul className={cn('divide-y', className)}>
            {notifications.items.map((item) => (
                <li key={item.id}>
                    <NotificationItem item={item} onNavigate={onNavigate} />
                </li>
            ))}
        </ul>
    );
}

function NotificationItem({ item, onNavigate }: { item: ContactNotification; onNavigate: () => void }) {
    const getInitials = useInitials();

    return (
        <Link
            href={show(item.id)}
            onClick={onNavigate}
            className="flex gap-3 px-4 py-3 transition-colors outline-none hover:bg-muted/60 focus-visible:bg-muted/60"
        >
            <span className="flex size-9 shrink-0 items-center justify-center rounded-full bg-emerald-500/10 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                {getInitials(item.name)}
            </span>
            <span className="min-w-0 flex-1">
                <span className="flex items-baseline justify-between gap-2">
                    <span className="truncate text-sm font-medium">{item.name}</span>
                    {item.received_ago && (
                        <span className="shrink-0 text-[11px] text-muted-foreground">{item.received_ago}</span>
                    )}
                </span>
                {item.project_type && (
                    <span className="block truncate text-xs text-emerald-600 dark:text-emerald-400">
                        {item.project_type}
                    </span>
                )}
                <span className="mt-0.5 line-clamp-2 text-xs text-muted-foreground">{item.excerpt}</span>
            </span>
            <span className="mt-1.5 size-2 shrink-0 rounded-full bg-emerald-500" aria-hidden />
        </Link>
    );
}
