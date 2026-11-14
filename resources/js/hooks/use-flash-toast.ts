import { router } from '@inertiajs/react';
import { useEffect } from 'react';
import { toast } from 'sonner';
import type { FlashToast } from '@/types/ui';

export function useFlashToast(): void {
    useEffect(() => {
        return router.on('flash', (event) => {
            const flash = (event as CustomEvent).detail?.flash;
            const data = flash?.toast as FlashToast | undefined;

            if (!data) {
                return;
            }

            // Same type + message reuses the toast on screen, so submitting twice never stacks duplicates.
            toast[data.type](data.message, { id: `${data.type}:${data.message}` });
        });
    }, []);
}
