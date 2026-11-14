import { Github, Star } from 'lucide-react';
import { Skeleton } from '@/components/ui/skeleton';
import { formatDate } from '@/lib/format';
import type { GitHubActivity as GitHubActivityData } from '@/types/domains/github';

/** Compact public GitHub summary: counts, language mix and recent repos. */
export function GitHubActivity({ activity }: { activity: GitHubActivityData }) {
    const stats = [
        { label: 'Public repos', value: activity.public_repos },
        { label: 'Stars', value: activity.total_stars },
        { label: 'Followers', value: activity.followers },
        ...(activity.contributions_last_year !== null
            ? [{ label: 'Contributions (1y)', value: activity.contributions_last_year }]
            : []),
    ];

    return (
        <div className="grid grid-cols-1 gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)]">
            <div className="space-y-6">
                <dl className="grid grid-cols-2 gap-4">
                    {stats.map((stat) => (
                        <div key={stat.label} className="rounded-lg border p-4">
                            <dt className="text-xs text-muted-foreground">{stat.label}</dt>
                            <dd className="mt-1 text-2xl font-semibold tabular-nums">{stat.value.toLocaleString()}</dd>
                        </div>
                    ))}
                </dl>
                {activity.languages.length > 0 && (
                    <div className="space-y-2">
                        <p className="text-sm font-medium">Languages</p>
                        <ul className="space-y-2">
                            {activity.languages.map((language) => (
                                <li key={language.name} className="space-y-1">
                                    <div className="flex justify-between text-xs text-muted-foreground">
                                        <span>{language.name}</span>
                                        <span className="tabular-nums">{language.share}%</span>
                                    </div>
                                    <div className="h-1.5 rounded-full bg-muted" aria-hidden>
                                        <div
                                            className="h-full rounded-full bg-brand"
                                            style={{ width: `${language.share}%` }}
                                        />
                                    </div>
                                </li>
                            ))}
                        </ul>
                    </div>
                )}
            </div>
            <div className="space-y-3">
                <p className="text-sm font-medium">Recently active</p>
                <ul className="divide-y rounded-xl border">
                    {activity.recent_repos.map((repo) => (
                        <li key={repo.url} className="px-4 py-3">
                            <a
                                href={repo.url}
                                target="_blank"
                                rel="noreferrer"
                                className="font-medium hover:text-brand"
                            >
                                {repo.name}
                            </a>
                            {repo.description && (
                                <p className="mt-0.5 line-clamp-2 text-sm text-muted-foreground">{repo.description}</p>
                            )}
                            <p className="mt-1 flex flex-wrap gap-x-3 text-xs text-muted-foreground">
                                {repo.language && <span>{repo.language}</span>}
                                <span className="inline-flex items-center gap-1">
                                    <Star className="size-3" aria-hidden /> {repo.stars}
                                </span>
                                {repo.pushed_at && <span>Updated {formatDate(repo.pushed_at)}</span>}
                            </p>
                        </li>
                    ))}
                </ul>
                <a
                    href={activity.profile_url}
                    target="_blank"
                    rel="noreferrer me"
                    className="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground"
                >
                    <Github className="size-4" aria-hidden /> @{activity.username} on GitHub
                </a>
            </div>
        </div>
    );
}

export function GitHubActivitySkeleton() {
    return (
        <div
            className="grid grid-cols-1 gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)]"
            aria-busy="true"
            aria-label="Loading GitHub activity"
        >
            <div className="grid grid-cols-2 gap-4">
                {[0, 1, 2, 3].map((key) => (
                    <Skeleton key={key} className="h-20" />
                ))}
            </div>
            <Skeleton className="h-56" />
        </div>
    );
}
