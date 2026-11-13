import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, ShieldCheck } from 'lucide-react';
import { AnimatedLogo } from '@/components/custom/animated-logo';
import AppLogoIcon from '@/components/custom/app-logo-icon';
import { DotFieldBackground } from '@/components/custom/dot-field-background';
import { Reveal } from '@/components/custom/reveal';
import { SectionLabel } from '@/components/custom/section-label';
import { Skeleton } from '@/components/ui/skeleton';
import { usePageTransition } from '@/hooks/use-page-transition';
import { home } from '@/routes';
import type { AuthLayoutProps } from '@/types';

/**
 * Sign-in and account-recovery pages, in the public site's theme: dot-field backdrop, a brand panel
 * with the mark drawing itself in (wide screens), and the form in a bordered card.
 */
export default function AuthSimpleLayout({ children, title, description }: AuthLayoutProps) {
    const { site } = usePage().props;
    const pendingPath = usePageTransition();

    return (
        <div className="relative isolate flex min-h-svh flex-col bg-background font-sans text-muted-foreground antialiased">
            <DotFieldBackground />

            <header className="mx-auto flex w-full max-w-6xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                <Link
                    href={home()}
                    className="group flex items-center gap-2 rounded-md text-sm font-semibold tracking-tight text-foreground"
                >
                    <AppLogoIcon className="size-6 text-primary" />
                    {site.name}
                </Link>
                <Link
                    href={home()}
                    className="group inline-flex items-center gap-1.5 rounded-md font-mono text-xs transition-colors duration-150 hover:text-foreground"
                >
                    <ArrowLeft
                        className="size-3.5 transition-transform duration-150 group-hover:-translate-x-0.5"
                        aria-hidden
                    />
                    back to site
                </Link>
            </header>

            <main className="mx-auto grid w-full max-w-6xl flex-1 items-center gap-12 px-4 pb-16 sm:px-6 lg:grid-cols-2 lg:gap-20 lg:px-8">
                <section aria-hidden className="hidden lg:block">
                    <AnimatedLogo className="size-28" />
                    <Reveal delay={0.7} className="mt-10 max-w-md space-y-4">
                        <SectionLabel>admin</SectionLabel>
                        <p className="text-3xl font-semibold tracking-tight text-foreground">
                            Everything behind {site.name.split(' ')[0]}&apos;s portfolio.
                        </p>
                        <p className="leading-relaxed">
                            Case studies, leads, bookings and availability, all in one place.
                        </p>
                    </Reveal>
                </section>

                <Reveal className="mx-auto w-full max-w-sm lg:mx-0 lg:justify-self-end">
                    <div className="rounded-lg border border-border bg-card p-6 shadow-sm backdrop-blur-sm sm:p-8">
                        <div className="mb-8 space-y-3">
                            <div className="flex size-10 items-center justify-center rounded-md border border-border bg-background lg:hidden">
                                <AppLogoIcon className="size-6 text-primary" />
                            </div>
                            <h1 className="text-xl font-semibold tracking-tight text-foreground">{title}</h1>
                            {description && <p className="text-sm leading-relaxed">{description}</p>}
                        </div>
                        {pendingPath ? <AuthFormSkeleton /> : null}
                        <div hidden={pendingPath !== null}>{children}</div>
                    </div>
                    <p className="mt-4 flex items-center justify-center gap-1.5 font-mono text-xs lg:justify-start">
                        <ShieldCheck className="size-3.5 text-primary" aria-hidden />
                        admin access only
                    </p>
                </Reveal>
            </main>
        </div>
    );
}

/** Placeholder fields while moving between auth pages (e.g. log in → forgot password). */
function AuthFormSkeleton() {
    return (
        <div role="status" aria-busy="true" className="space-y-5">
            <span className="sr-only">Loading…</span>
            {[0, 1].map((key) => (
                <div key={key} aria-hidden className="space-y-2">
                    <Skeleton className="h-3.5 w-24" />
                    <Skeleton className="h-9 w-full" />
                </div>
            ))}
            <Skeleton aria-hidden className="h-9 w-full" />
        </div>
    );
}
