import { useSyncExternalStore } from 'react';

const mql = typeof window === 'undefined' ? undefined : window.matchMedia('(prefers-reduced-motion: reduce)');

function subscribe(callback: (event: MediaQueryListEvent) => void) {
    if (!mql) {
        return () => {};
    }

    mql.addEventListener('change', callback);

    return () => mql.removeEventListener('change', callback);
}

/** True when the visitor asked the OS for less motion. */
export function useReducedMotion(): boolean {
    return useSyncExternalStore(
        subscribe,
        () => mql?.matches ?? false,
        () => false,
    );
}
