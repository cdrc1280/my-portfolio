import { Head, Link } from '@inertiajs/react';
import { Inbox, Lightbulb } from 'lucide-react';
import { AdminSplit, BreakdownList, countBy, SidePanel, StatGrid, TipList } from '@/components/custom/admin-split';
import { MessageList } from '@/components/custom/contact/message-list';
import { PageHeader } from '@/components/custom/page-header';
import { Pagination } from '@/components/custom/pagination';
import { cn } from '@/lib/utils';
import { index } from '@/routes/admin/messages';
import type { Paginated } from '@/types';
import type { ContactMessage, InboxFilter } from '@/types/domains/contact';

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

    const page = messages.data;
    const aside = (
        <>
            <SidePanel title="Inbox" icon={Inbox}>
                <StatGrid
                    stats={[
                        { label: 'Unread', value: unreadCount },
                        { label: filter === 'unread' ? 'Unread shown' : 'Total', value: messages.meta.total },
                    ]}
                />
            </SidePanel>
            <SidePanel title="On this page">
                <BreakdownList
                    items={countBy(page, (message) => message.project_type ?? 'General enquiry')}
                    empty="No messages to break down."
                />
                {page.length > 0 && (
                    <p className="mt-3 text-xs text-muted-foreground">
                        {page.filter((message) => message.budget).length} of {page.length} gave a budget.
                    </p>
                )}
            </SidePanel>
            <SidePanel title="Tips" icon={Lightbulb}>
                <TipList
                    tips={[
                        'Opening a message marks it read. Use "Mark unread" to keep it on your list.',
                        'Reply opens your mail app with the subject filled in.',
                    ]}
                />
            </SidePanel>
        </>
    );

    return (
        <>
            <Head title="Inbox" />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader title="Inbox" description="Messages sent through the contact form." />
                <AdminSplit aside={aside}>
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
                </AdminSplit>
            </div>
        </>
    );
}

MessagesIndex.layout = {
    breadcrumbs: [{ title: 'Inbox', href: index() }],
};
