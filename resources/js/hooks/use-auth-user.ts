import { usePage } from '@inertiajs/react';
import type { User } from '@/types';

/**
 * The signed-in user, for pages that sit behind the `auth` middleware.
 * Throws if used on a guest page, which is always a programming error.
 */
export function useAuthUser(): User {
    const { user } = usePage().props.auth;

    if (user === null) {
        throw new Error('useAuthUser() called on a page without an authenticated user.');
    }

    return user;
}
