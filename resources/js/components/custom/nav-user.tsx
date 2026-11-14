import { usePage } from '@inertiajs/react';
import { UserMenuContent } from '@/components/custom/user-menu-content';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { useInitials } from '@/hooks/use-initials';

/**
 * Account menu at the top right of the admin header, right of the notification bell: an avatar that
 * opens name and email, Settings and Log out.
 */
export function NavUser() {
    const { auth } = usePage().props;
    const getInitials = useInitials();

    if (!auth.user) {
        return null;
    }

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button
                    variant="ghost"
                    size="icon"
                    className="size-9 rounded-full p-0 data-[state=open]:ring-2 data-[state=open]:ring-ring/40"
                    aria-label="Account menu"
                    data-test="user-menu-button"
                >
                    <Avatar className="size-8 overflow-hidden rounded-full">
                        <AvatarFallback className="rounded-full bg-neutral-200 text-xs font-medium text-black dark:bg-neutral-700 dark:text-white">
                            {getInitials(auth.user.name)}
                        </AvatarFallback>
                    </Avatar>
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent className="w-60 rounded-lg" align="end" sideOffset={8}>
                <UserMenuContent user={auth.user} />
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
