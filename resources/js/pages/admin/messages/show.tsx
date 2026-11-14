import { Head, Link, router, setLayoutProps } from '@inertiajs/react';
import { ArrowLeft, ClipboardList, Mail, MailOpen, Trash2, User } from 'lucide-react';
import { AdminSplit, FactList, SidePanel } from '@/components/custom/admin-split';
import { ConfirmDialog } from '@/components/custom/confirm-dialog';
import { Button } from '@/components/ui/button';
import { formatDateTime } from '@/lib/format';
import { destroy, index, show, unread } from '@/routes/admin/messages';
import type { ContactMessage } from '@/types/domains/contact';

export default function ShowMessage({ message }: { message: ContactMessage }) {
    setLayoutProps({
        breadcrumbs: [
            { title: 'Inbox', href: index() },
            { title: message.name, href: show(message.id) },
        ],
    });

    const replySubject = encodeURIComponent(`Re: ${message.project_type ?? 'Your message'}`);
    const brief = [
        { term: 'Project type', value: message.project_type },
        { term: 'Budget', value: message.budget },
        { term: 'Timeline', value: message.timeline },
    ];

    return (
        <>
            <Head title={`Message from ${message.name}`} />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <div className="flex flex-wrap items-center justify-between gap-2">
                    <Button variant="ghost" size="sm" asChild>
                        <Link href={index()}>
                            <ArrowLeft aria-hidden /> Inbox
                        </Link>
                    </Button>
                    <div className="flex flex-wrap gap-2">
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

                <AdminSplit
                    aside={
                        <>
                            <SidePanel title="Sender" icon={User}>
                                <FactList
                                    facts={[
                                        { term: 'Name', value: message.name },
                                        {
                                            term: 'Email',
                                            value: (
                                                <a href={`mailto:${message.email}`} className="hover:underline">
                                                    {message.email}
                                                </a>
                                            ),
                                        },
                                        {
                                            term: 'Received',
                                            value: message.created_at ? formatDateTime(message.created_at) : 'Unknown',
                                        },
                                    ]}
                                />
                            </SidePanel>
                            <SidePanel title="Project brief" icon={ClipboardList}>
                                <FactList
                                    facts={brief.map((item) => ({ term: item.term, value: item.value ?? 'Not given' }))}
                                />
                            </SidePanel>
                        </>
                    }
                >
                    <article className="min-w-0 space-y-4 rounded-xl border p-4 sm:p-6">
                        <header className="space-y-1 border-b pb-4">
                            <h1 className="text-xl font-semibold tracking-tight">
                                {message.project_type ?? 'General enquiry'}
                            </h1>
                            <p className="text-sm break-words">
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
                        <p className="max-w-prose leading-relaxed break-words whitespace-pre-line">{message.message}</p>
                    </article>
                </AdminSplit>
            </div>
        </>
    );
}
