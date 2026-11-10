import { Head, Link, router, setLayoutProps } from '@inertiajs/react';
import { ArrowLeft, Mail, MailOpen, Trash2 } from 'lucide-react';
import { ConfirmDialog } from '@/components/shared/confirm-dialog';
import { Button } from '@/components/ui/button';
import type { ContactMessage } from '@/features/contact/types';
import { formatDateTime } from '@/lib/format';
import { destroy, index, show, unread } from '@/routes/admin/messages';

export default function ShowMessage({ message }: { message: ContactMessage }) {
    setLayoutProps({
        breadcrumbs: [
            { title: 'Inbox', href: index() },
            { title: message.name, href: show(message.id) },
        ],
    });

    const replySubject = encodeURIComponent(`Re: ${message.subject ?? 'Your message'}`);

    return (
        <>
            <Head title={`Message from ${message.name}`} />
            <div className="flex max-w-3xl flex-col gap-6 p-4 md:p-6">
                <div className="flex flex-wrap items-center justify-between gap-2">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href={index()}>
                            <ArrowLeft aria-hidden /> Inbox
                        </Link>
                    </Button>
                    <div className="flex gap-2">
                        <Button asChild>
                            <a href={`mailto:${message.email}?subject=${replySubject}`}>
                                <Mail aria-hidden /> Reply
                            </a>
                        </Button>
                        <Button variant="outline" onClick={() => router.patch(unread.url(message.id))}>
                            <MailOpen aria-hidden /> Mark unread
                        </Button>
                        <ConfirmDialog
                            title="Delete this message?"
                            description="This cannot be undone."
                            confirmLabel="Delete"
                            onConfirm={() => router.delete(destroy.url(message.id))}
                            trigger={
                                <Button variant="outline" size="icon" aria-label="Delete message">
                                    <Trash2 aria-hidden />
                                </Button>
                            }
                        />
                    </div>
                </div>

                <article className="space-y-4 rounded-xl border p-6">
                    <header className="space-y-1 border-b pb-4">
                        <h1 className="text-xl font-semibold tracking-tight">{message.subject ?? 'No subject'}</h1>
                        <p className="text-sm">
                            {message.name}{' '}
                            <a href={`mailto:${message.email}`} className="text-muted-foreground hover:underline">
                                &lt;{message.email}&gt;
                            </a>
                        </p>
                        {message.created_at && (
                            <p className="text-xs text-muted-foreground">
                                <time dateTime={message.created_at}>{formatDateTime(message.created_at)}</time>
                            </p>
                        )}
                    </header>
                    <p className="leading-relaxed whitespace-pre-line">{message.message}</p>
                </article>
            </div>
        </>
    );
}
