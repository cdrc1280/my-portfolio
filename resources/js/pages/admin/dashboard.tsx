import { Deferred, Head, Link, usePage } from '@inertiajs/react';
import type { LucideIcon } from 'lucide-react';
import {
    CalendarClock,
    FolderKanban,
    Github,
    Inbox,
    ListChecks,
    Newspaper,
    Plus,
    Radio,
    Sparkles,
    Users,
} from 'lucide-react';
import { MessageList } from '@/components/custom/contact/message-list';
import { AvailabilityControl } from '@/components/custom/dashboard/availability-control';
import { BentoTile } from '@/components/custom/dashboard/bento-tile';
import { ContentHealth } from '@/components/custom/dashboard/content-health';
import { LeadsSummary } from '@/components/custom/dashboard/leads-summary';
import { UpcomingCalls, UpcomingCallsSkeleton } from '@/components/custom/dashboard/upcoming-calls';
import { GitHubActivity, GitHubActivitySkeleton } from '@/components/custom/github/github-activity';
import { PageHeader } from '@/components/custom/page-header';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/admin';
import { index as messagesIndex } from '@/routes/admin/messages';
import { create as createPost, index as postsIndex } from '@/routes/admin/posts';
import { create as createProject, index as projectsIndex } from '@/routes/admin/projects';
import { index as tagsIndex } from '@/routes/admin/tags';
import type { ContactMessage } from '@/types/domains/contact';
import type {
    AvailabilityControlProps,
    BookingsState,
    DashboardStats,
    HealthCheck,
    LeadStats,
} from '@/types/domains/dashboard';
import type { GitHubActivity as GitHubActivityData } from '@/types/domains/github';

type Props = {
    stats: DashboardStats;
    recentMessages: ContactMessage[];
    leads: LeadStats;
    health: HealthCheck[];
    availability: AvailabilityControlProps;
    /** Deferred: undefined until loaded. */
    bookings?: BookingsState;
    /** Deferred: undefined until loaded, null when GitHub is not configured or unavailable. */
    github?: GitHubActivityData | null;
};

const greeting = () => {
    const hour = new Date().getHours();

    return hour < 12 ? 'Good morning' : hour < 18 ? 'Good afternoon' : 'Good evening';
};

/** Bento dashboard: calls, availability, leads, content health, counts, inbox and GitHub. */
export default function Dashboard({ stats, recentMessages, leads, health, availability, bookings, github }: Props) {
    const { site } = usePage().props;

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title={`${greeting()}, ${site.name.split(' ')[0]}`}
                    description="Your calls, leads and what still needs attention."
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

                <div className="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12">
                    <BentoTile
                        title="Upcoming calls"
                        icon={CalendarClock}
                        className="md:col-span-2 xl:col-span-8 xl:row-span-2"
                    >
                        <Deferred data="bookings" fallback={<UpcomingCallsSkeleton />}>
                            {bookings ? <UpcomingCalls state={bookings} bookingUrl={site.booking_url} /> : <></>}
                        </Deferred>
                    </BentoTile>

                    <BentoTile title="Availability" icon={Radio} className="xl:col-span-4">
                        <AvailabilityControl {...availability} />
                    </BentoTile>

                    <BentoTile
                        title="Leads"
                        icon={Users}
                        className="xl:col-span-4"
                        action={
                            <Button variant="ghost" size="sm" asChild>
                                <Link href={messagesIndex()}>Inbox</Link>
                            </Button>
                        }
                    >
                        <LeadsSummary leads={leads} />
                    </BentoTile>

                    <StatTile
                        icon={FolderKanban}
                        label="Projects"
                        value={stats.published_projects}
                        detail={`${stats.draft_projects} draft`}
                        href={projectsIndex.url()}
                    />
                    <StatTile
                        icon={Newspaper}
                        label="Posts"
                        value={stats.published_posts}
                        detail={`${stats.draft_posts} draft`}
                        href={postsIndex.url()}
                    />
                    <StatTile icon={Sparkles} label="Skills" value={stats.skills} href={tagsIndex.url()} />
                    <StatTile
                        icon={Inbox}
                        label="Messages"
                        value={stats.unread_messages}
                        detail={`unread of ${stats.total_messages}`}
                        href={messagesIndex.url()}
                    />

                    <BentoTile title="Content health" icon={ListChecks} className="xl:col-span-6">
                        <ContentHealth checks={health} />
                    </BentoTile>

                    <BentoTile
                        title="Recent messages"
                        icon={Inbox}
                        className="xl:col-span-6"
                        action={
                            <Button variant="ghost" size="sm" asChild>
                                <Link href={messagesIndex()}>Open inbox</Link>
                            </Button>
                        }
                    >
                        <MessageList messages={recentMessages} />
                    </BentoTile>

                    <BentoTile title="GitHub" icon={Github} className="md:col-span-2 xl:col-span-12">
                        <Deferred data="github" fallback={<GitHubActivitySkeleton />}>
                            {github ? (
                                <GitHubActivity activity={github} />
                            ) : (
                                <p className="text-sm text-muted-foreground">
                                    Set <code className="font-mono text-xs">GITHUB_USERNAME</code> in{' '}
                                    <code className="font-mono text-xs">.env</code> to show your activity.
                                </p>
                            )}
                        </Deferred>
                    </BentoTile>
                </div>
            </div>
        </>
    );
}

function StatTile({
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
            className="group min-w-0 rounded-xl border bg-card p-5 transition-colors hover:border-foreground/20 xl:col-span-3"
        >
            <div className="flex items-start justify-between gap-4">
                <div>
                    <p className="text-sm text-muted-foreground">{label}</p>
                    <p className="mt-1 text-3xl font-semibold tabular-nums">{value}</p>
                    {detail && <p className="mt-1 text-xs text-muted-foreground">{detail}</p>}
                </div>
                <Icon className="size-5 text-muted-foreground transition-colors group-hover:text-primary" aria-hidden />
            </div>
        </Link>
    );
}

Dashboard.layout = {
    breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
};
