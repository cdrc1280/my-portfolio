import { Head } from '@inertiajs/react';
import { PageHeader } from '@/components/custom/page-header';
import { index } from '@/routes/admin/board';
import type { SelectOption } from '@/types';
import type { BoardColumn, CardPriority, CardStatus } from '@/types/domains/board';
import { KanbanBoard } from './kanban-board';

type Props = {
    columns: BoardColumn[];
    statuses: SelectOption<CardStatus>[];
    priorities: SelectOption<CardPriority>[];
};

export default function BoardIndex({ columns, statuses, priorities }: Props) {
    const open = columns.filter((column) => column.status !== 'done').flatMap((column) => column.cards);
    const overdue = open.filter((card) => card.overdue).length;

    return (
        <>
            <Head title="Board" />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Board"
                    description={
                        <>
                            Your private space for tasks, ideas and notes. {open.length} open
                            {overdue > 0 && <span className="text-destructive"> · {overdue} overdue</span>}.
                        </>
                    }
                />
                <KanbanBoard columns={columns} statuses={statuses} priorities={priorities} />
            </div>
        </>
    );
}

BoardIndex.layout = {
    breadcrumbs: [{ title: 'Board', href: index() }],
};
