import { createInertiaApp } from '@inertiajs/react';
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
                <Toaster closeButton />
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
