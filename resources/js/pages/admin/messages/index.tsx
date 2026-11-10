import { Head, Link } from '@inertiajs/react';
import { PageHeader } from '@/components/shared/page-header';
import { Pagination } from '@/components/shared/pagination';
import { MessageList } from '@/features/contact/components/message-list';
import type { ContactMessage, InboxFilter } from '@/features/contact/types';
import { cn } from '@/lib/utils';
import { index } from '@/routes/admin/messages';
import type { Paginated } from '@/types';

type Props = {
    messages: Paginated<ContactMessage>;
    filter: InboxFilter;
    unreadCount: number;
};

export default function MessagesIndex({ messages, filter, unreadCount }: Props) {
    const tabs: Array<{ value: InboxFilter; label: string }> = [
        { value: 'all', label: 'All' },
        { value: 'unread', label: `Unread (${unreadCount})` },
    ];

    return (
        <>
            <Head title="Inbox" />
            <div className="flex max-w-4xl flex-col gap-6 p-4 md:p-6">
                <PageHeader title="Inbox" description="Messages sent through the contact form." />
                <nav aria-label="Filter messages" className="flex w-fit gap-1 rounded-lg bg-muted p-1 text-sm">
                    {tabs.map((tab) => (
                        <Link
                            key={tab.value}
                            href={index({ query: tab.value === 'all' ? {} : { filter: tab.value } })}
                            aria-current={filter === tab.value ? 'page' : undefined}
                            className={cn(
                                'rounded-md px-3 py-1.5 transition-colors',
                                filter === tab.value
                                    ? 'bg-background shadow-xs'
                                    : 'text-muted-foreground hover:text-foreground',
                            )}
                        >
                            {tab.label}
                        </Link>
                    ))}
                </nav>
                <MessageList messages={messages.data} />
                <Pagination paginator={messages} />
            </div>
        </>
    );
}

MessagesIndex.layout = {
    breadcrumbs: [{ title: 'Inbox', href: index() }],
};
