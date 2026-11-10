import { Link, usePage } from '@inertiajs/react';
import { Menu } from 'lucide-react';
import type { ReactNode } from 'react';
import { ThemeToggle } from '@/components/shared/theme-toggle';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { publicNavigation } from '@/layouts/public/navigation';
import { cn, toUrl } from '@/lib/utils';
import { home } from '@/routes';

/** Portfolio-facing shell: slim header, centred content column, footer. */
export default function PublicLayout({ children }: { children: ReactNode }) {
    const { name } = usePage().props;

    return (
        <div className="flex min-h-svh flex-col">
            <a
                href="#main"
                className="sr-only rounded-md bg-background px-3 py-2 focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:ring-2 focus:ring-ring"
            >
                Skip to content
            </a>

            <header className="sticky top-0 z-40 border-b bg-background/85 backdrop-blur supports-[backdrop-filter]:bg-background/70">
                <div className="mx-auto flex h-16 max-w-5xl items-center justify-between gap-6 px-4 sm:px-6">
                    <Link href={home()} className="font-semibold tracking-tight">
                        {name}
                    </Link>

                    <div className="flex items-center gap-1">
                        <nav aria-label="Main" className="hidden sm:block">
                            <NavLinks />
                        </nav>
                        <ThemeToggle />
                        <MobileMenu />
                    </div>
                </div>
            </header>

            <main id="main" className="mx-auto w-full max-w-5xl flex-1 px-4 py-12 sm:px-6 sm:py-16">
                {children}
            </main>

            <footer className="border-t">
                <div className="mx-auto flex max-w-5xl flex-col gap-2 px-4 py-8 text-sm text-muted-foreground sm:flex-row sm:justify-between sm:px-6">
                    <p>
                        © {new Date().getFullYear()} {name}
                    </p>
                </div>
            </footer>
        </div>
    );
}

function NavLinks({ vertical = false }: { vertical?: boolean }) {
    const { isCurrentOrParentUrl } = useCurrentUrl();

    return (
        <ul className={cn('flex gap-1', vertical && 'flex-col')}>
            {publicNavigation.map((item) => {
                const active = isCurrentOrParentUrl(item.href);

                return (
                    <li key={toUrl(item.href)}>
                        <Link
                            href={item.href}
                            aria-current={active ? 'page' : undefined}
                            className={cn(
                                'block rounded-md px-3 py-2 text-sm transition-colors hover:text-foreground',
                                active ? 'font-medium text-foreground' : 'text-muted-foreground',
                            )}
                        >
                            {item.title}
                        </Link>
                    </li>
                );
            })}
        </ul>
    );
}

function MobileMenu() {
    return (
        <Sheet>
            <SheetTrigger asChild>
                <Button variant="ghost" size="icon" className="sm:hidden" aria-label="Open menu">
                    <Menu className="size-5" aria-hidden />
                </Button>
            </SheetTrigger>
            <SheetContent side="right" className="w-64">
                <SheetHeader>
                    <SheetTitle>Menu</SheetTitle>
                </SheetHeader>
                <nav aria-label="Mobile" className="px-2">
                    <NavLinks vertical />
                </nav>
            </SheetContent>
        </Sheet>
    );
}
