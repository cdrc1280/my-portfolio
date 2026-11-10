import { router } from '@inertiajs/react';
import { useState } from 'react';

type Identifiable = { id: number };

/**
 * Optimistic drag-to-reorder: shows the new order immediately, POSTs the
 * ids, then falls back to whatever order the server returns.
 */
export function useReorder<TItem extends Identifiable>(items: TItem[], url: string) {
    const [pendingOrder, setPendingOrder] = useState<number[] | null>(null);

    const ordered = pendingOrder
        ? pendingOrder.map((id) => items.find((item) => item.id === id)).filter((item) => item !== undefined)
        : items;

    const reorder = (next: TItem[]) => {
        const ids = next.map((item) => item.id);
        setPendingOrder(ids);

        router.post(url, { ids }, { preserveScroll: true, onFinish: () => setPendingOrder(null) });
    };

    return { items: ordered, reorder, saving: pendingOrder !== null };
}
