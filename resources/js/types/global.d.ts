import type { Auth } from '@/types/auth';
import type { SiteProfile } from '@/types/resources';

declare module 'react' {
    interface InputHTMLAttributes<T> {
        passwordrules?: string;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            appUrl: string;
            site: SiteProfile;
            /** Which optional public pages have content (they are left out of navigation when empty). */
            content: { services: boolean; uses: boolean };
            auth: Auth;
            sidebarOpen: boolean;
            [key: string]: unknown;
        };
    }
}
