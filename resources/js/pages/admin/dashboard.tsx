import { Head, Link } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import { FolderKanban, Inbox, Newspaper, Plus, Sparkles } from 'lucide-react';
import { PageHeader } from '@/components/shared/page-header';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { MessageList } from '@/features/contact/components/message-list';
import type { ContactMessage } from '@/features/contact/types';
import { dashboard } from '@/routes/admin';
import { index as messagesIndex } from '@/routes/admin/messages';
import { create as createPost, index as postsIndex } from '@/routes/admin/posts';
import { create as createProject, index as projectsIndex } from '@/routes/admin/projects';
import { index as skillsIndex } from '@/routes/admin/skills';

type Stats = {
    published_projects: number;
    draft_projects: number;
    published_posts: number;
    draft_posts: number;
    skills: number;
    unread_messages: number;
    total_messages: number;
};

type Props = {
    stats: Stats;
    recentMessages: ContactMessage[];
};

export default function Dashboard({ stats, recentMessages }: Props) {
    return (
        <>
            <Head title="Dashboard" />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Dashboard"
                    actions={
                        <>
                            <Button variant="outline" asChild>
                                <Link href={createPost()}>
                                    <Plus aria-hidden /> Post
                                </Link>
                            </Button>
                            <Button asChild>
                                <Link href={createProject()}>
                                    <Plus aria-hidden /> Project
                                </Link>
                            </Button>
                        </>
                    }
                />

                <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <StatCard
                        icon={FolderKanban}
                        label="Projects"
                        value={stats.published_projects}
                        detail={`${stats.draft_projects} draft`}
                        href={projectsIndex.url()}
                    />
                    <StatCard
                        icon={Newspaper}
                        label="Posts"
                        value={stats.published_posts}
                        detail={`${stats.draft_posts} draft`}
                        href={postsIndex.url()}
                    />
                    <StatCard icon={Sparkles} label="Skills" value={stats.skills} href={skillsIndex.url()} />
                    <StatCard
                        icon={Inbox}
                        label="Unread messages"
                        value={stats.unread_messages}
                        detail={`${stats.total_messages} total`}
                        href={messagesIndex.url()}
                    />
                </div>

                <Card>
                    <CardHeader className="flex-row items-center justify-between">
                        <CardTitle>Recent messages</CardTitle>
                        <Button variant="ghost" size="sm" asChild>
                            <Link href={messagesIndex()}>Open inbox</Link>
                        </Button>
                    </CardHeader>
                    <CardContent>
                        <MessageList messages={recentMessages} />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

function StatCard({
    icon: Icon,
    label,
    value,
    detail,
    href,
}: {
    icon: LucideIcon;
    label: string;
    value: number;
    detail?: string;
    href: string;
}) {
    return (
        <Link
            href={href}
            className="group rounded-xl focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
        >
            <Card className="h-full transition-colors group-hover:border-foreground/20">
                <CardContent className="flex items-start justify-between gap-4 pt-6">
                    <div>
                        <p className="text-sm text-muted-foreground">{label}</p>
                        <p className="mt-1 text-3xl font-semibold tabular-nums">{value}</p>
                        {detail && <p className="mt-1 text-xs text-muted-foreground">{detail}</p>}
                    </div>
                    <Icon className="size-5 text-muted-foreground" aria-hidden />
                </CardContent>
            </Card>
        </Link>
    );
}

Dashboard.layout = {
    breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
};
