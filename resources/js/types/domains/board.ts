/** App\Domains\Board\Enums\CardStatus: the board's columns, left to right. */
export type CardStatus = 'ideas' | 'todo' | 'doing' | 'done';

/** App\Domains\Board\Enums\CardPriority */
export type CardPriority = 'low' | 'normal' | 'high';

/** App\Domains\Board\Resources\BoardCardResource */
export type BoardCard = {
    id: number;
    title: string;
    notes: string | null;
    status: CardStatus;
    priority: CardPriority;
    priority_label: string;
    due_on: string | null;
    overdue: boolean;
    due_today: boolean;
    position: number;
};

/** One column from App\Domains\Board\Queries\BoardQueries::columns(). */
export type BoardColumn = {
    status: CardStatus;
    label: string;
    cards: BoardCard[];
};
