import { createInertiaApp } from '@inertiajs/react';
import type { ToastClassnames } from 'sonner';
import { AppErrorBoundary } from '@/components/custom/app-error-boundary';
import { HttpErrorFallback } from '@/components/custom/http-error-fallback';
import { Toaster } from '@/components/ui/sonner';
import { TooltipProvider } from '@/components/ui/tooltip';
import { initializeTheme } from '@/hooks/use-appearance';
import AdminLayout from '@/layouts/admin-layout';
import AuthLayout from '@/layouts/auth-layout';
import PublicLayout from '@/layouts/public-layout';
import SettingsLayout from '@/layouts/settings/layout';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

/**
 * Toast look: a card with a coloured accent bar and icon per type. Sonner's own styles use attribute
 * selectors, so these need `!` to win.
 */
const toastClassNames: ToastClassnames = {
    toast: 'items-start! gap-3! rounded-xl! border-border! bg-popover! py-3.5! pr-10! pl-5! shadow-lg! shadow-black/5! dark:shadow-black/40! before:absolute before:inset-y-3 before:left-2 before:w-[3px] before:rounded-full before:bg-border',
    icon: 'mt-px! size-5! [&>svg]:size-5!',
    title: 'text-sm! leading-snug! font-medium!',
    description: 'text-xs! text-muted-foreground!',
    closeButton:
        'top-3! right-2! left-auto! translate-x-0! translate-y-0! border-0! bg-transparent! text-muted-foreground! hover:bg-muted! hover:text-foreground!',
    success: 'before:bg-emerald-500! [&_[data-icon]]:text-emerald-500',
    error: 'before:bg-red-500! [&_[data-icon]]:text-red-500',
    warning: 'before:bg-amber-500! [&_[data-icon]]:text-amber-500',
    info: 'before:bg-sky-500! [&_[data-icon]]:text-sky-500',
};

void createInertiaApp({
    title: (title) => (title ? `${title} · ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            case name.startsWith('public/'):
                return PublicLayout;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AdminLayout, SettingsLayout];
            case name.startsWith('admin/'):
                return AdminLayout;
            default:
                return null;
        }
    },
    strictMode: true,
    withApp(app) {
        return (
            <TooltipProvider delayDuration={0}>
                {/* A page that throws while rendering shows a recovery screen, never a blank page. */}
                <AppErrorBoundary>{app}</AppErrorBoundary>
                {/* Also renders Laravel flash toasts (Inertia::flash('toast', ...)). */}
                <Toaster
                    closeButton
                    position="top-right"
                    // Clears the 56px sticky site header so a toast never covers its buttons.
                    offset={{ top: 72, right: 16 }}
                    mobileOffset={{ top: 64, right: 12, left: 12 }}
                    duration={5000}
                    toastOptions={{ classNames: toastClassNames }}
                />
                <HttpErrorFallback />
            </TooltipProvider>
        );
    },
    // No progress bar: page changes show layout-specific skeletons (usePageTransition) and form
    // buttons show their own spinners.
    progress: false,
});

// This will set light / dark mode on load...
initializeTheme();
