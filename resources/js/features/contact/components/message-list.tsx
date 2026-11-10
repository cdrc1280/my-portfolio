import { Link } from '@inertiajs/react';
import { Inbox } from 'lucide-react';
import { EmptyState } from '@/components/shared/empty-state';
import type { ContactMessage } from '@/features/contact/types';
import { formatDateTime } from '@/lib/format';
import { cn } from '@/lib/utils';
import { show } from '@/routes/admin/messages';

export function MessageList({ messages }: { messages: ContactMessage[] }) {
    if (messages.length === 0) {
        return (
            <EmptyState
                icon={Inbox}
                title="Inbox zero"
                description="New messages from the contact form show up here."
            />
        );
    }

    return (
        <ul className="divide-y rounded-xl border">
            {messages.map((message) => (
                <li key={message.id}>
                    <Link
                        href={show(message.id)}
                        className="flex gap-3 px-4 py-3 transition-colors hover:bg-muted/50 focus-visible:bg-muted/50 focus-visible:outline-none"
                    >
                        <span
                            className={cn(
                                'mt-2 size-2 shrink-0 rounded-full',
                                message.is_read ? 'bg-transparent' : 'bg-brand',
                            )}
                            aria-label={message.is_read ? undefined : 'Unread'}
                        />
                        <div className="min-w-0 flex-1">
                            <div className="flex items-baseline justify-between gap-3">
                                <p className={cn('truncate', !message.is_read && 'font-semibold')}>{message.name}</p>
                                {message.created_at && (
                                    <time
                                        dateTime={message.created_at}
                                        className="shrink-0 text-xs text-muted-foreground"
                                    >
                                        {formatDateTime(message.created_at)}
                                    </time>
                                )}
                            </div>
                            <p
                                className={cn(
                                    'truncate text-sm',
                                    message.is_read ? 'text-muted-foreground' : 'font-medium',
                                )}
                            >
                                {message.subject ?? 'No subject'}
                            </p>
                            <p className="truncate text-sm text-muted-foreground">{message.excerpt}</p>
                        </div>
                    </Link>
                </li>
            ))}
        </ul>
    );
}
