import { Link, usePage } from '@inertiajs/react';
import { Menu } from 'lucide-react';
import AppLogoIcon from '@/components/custom/app-logo-icon';
import { containers } from '@/components/custom/section';
import { SocialLinks } from '@/components/custom/social-links';
import { ThemeToggle } from '@/components/custom/theme-toggle';
import { Button } from '@/components/ui/button';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { publicNavigation } from '@/layouts/public/navigation';
import { cn, toUrl } from '@/lib/utils';
import { home } from '@/routes';
import { show as contact } from '@/routes/contact';

/** Sticky, blurred site header: name on the left, navigation, theme toggle and "Hire me" on the right. */
export function SiteHeader({ name }: { name: string }) {
    return (
        <header className="sticky top-0 z-50 border-b border-border/80 bg-background/75 backdrop-blur-md supports-[backdrop-filter]:bg-background/60">
            <div className={cn(containers.page, 'flex h-14 items-center justify-between gap-4')}>
                <Link
                    href={home()}
                    className="group flex items-center gap-2 rounded-md text-sm font-semibold tracking-tight text-foreground"
                >
                    <AppLogoIcon className="size-6 text-primary transition-transform duration-200 group-hover:-translate-y-px" />
                    {name}
                </Link>

                <nav aria-label="Main" className="hidden md:block">
                    <NavLinks />
                </nav>

                <div className="flex items-center gap-1">
                    <ThemeToggle />
                    <Button asChild size="sm" className="hidden sm:inline-flex">
                        <Link href={contact()}>Hire me</Link>
                    </Button>
                    <MobileMenu />
                </div>
            </div>
        </header>
    );
}

function NavLinks({ vertical = false }: { vertical?: boolean }) {
    const { isCurrentOrParentUrl } = useCurrentUrl();
    const { site, content } = usePage().props;

    return (
        <ul className={cn('flex', vertical ? 'flex-col gap-1' : 'items-center gap-1')}>
            {publicNavigation({ blog: site.blog_enabled, ...content }).map((item) => {
                const active = isCurrentOrParentUrl(item.href);

                return (
                    <li key={toUrl(item.href)}>
                        <Link
                            href={item.href}
                            aria-current={active ? 'page' : undefined}
                            className={cn(
                                'block rounded-md px-3 py-2 text-sm transition-colors duration-150 hover:text-foreground',
                                vertical && 'text-base',
                                active ? 'text-primary' : 'text-muted-foreground',
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
                <Button variant="ghost" size="icon" className="md:hidden" aria-label="Open menu">
                    <Menu className="size-5" aria-hidden />
                </Button>
            </SheetTrigger>
            <SheetContent side="right" className="w-full max-w-xs">
                <SheetHeader>
                    <SheetTitle className="font-mono text-xs font-normal text-muted-foreground">{'// menu'}</SheetTitle>
                </SheetHeader>
                <nav aria-label="Mobile" className="px-2">
                    <NavLinks vertical />
                </nav>
                <div className="mt-auto space-y-4 p-4">
                    <Button asChild className="w-full">
                        <Link href={contact()}>Hire me</Link>
                    </Button>
                    <SocialLinks className="-ml-2" />
                </div>
            </SheetContent>
        </Sheet>
    );
}
