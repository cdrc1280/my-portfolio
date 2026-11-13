import { useHttpErrorFallback } from '@/hooks/use-http-error-fallback';

/** Mounts the global HTTP/network error toasts once, next to the Toaster. */
export function HttpErrorFallback() {
    useHttpErrorFallback();

    return null;
}
