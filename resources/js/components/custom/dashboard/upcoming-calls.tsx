import { CalendarClock, ExternalLink, Video } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { cn } from '@/lib/utils';
import type { Booking, BookingsState } from '@/types/domains/dashboard';

const DAYS = 14;
const dayKey = (date: Date) => `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`;
const time = new Intl.DateTimeFormat(undefined, { hour: 'numeric', minute: '2-digit' });
const dayLabel = new Intl.DateTimeFormat(undefined, { weekday: 'long', month: 'short', day: 'numeric' });
const weekday = new Intl.DateTimeFormat(undefined, { weekday: 'narrow' });

/**
 * Calendar monitoring: a 14-day strip marking days with booked calls, then the calls grouped by
 * day with join links. Times are shown in the browser's timezone.
 */
export function UpcomingCalls({ state, bookingUrl }: { state: BookingsState; bookingUrl: string | null }) {
    if (!state.configured) {
        return (
            <div className="space-y-3 text-sm">
                <p className="text-muted-foreground">
                    Connect Calendly to see calls clients book through your site, right here.
                </p>
                <ol className="list-decimal space-y-1.5 pl-5 text-muted-foreground">
                    <li>
                        In Calendly, open{' '}
                        <span className="font-medium text-foreground">Integrations → API & Webhooks</span> and generate
                        a personal access token.
                    </li>
                    <li>
                        Add it to <code className="rounded bg-muted px-1 font-mono text-xs">.env</code> as{' '}
                        <code className="rounded bg-muted px-1 font-mono text-xs">CALENDLY_TOKEN=…</code>
                    </li>
                    <li>Reload this page.</li>
                </ol>
                <Button asChild variant="outline" size="sm">
                    <a href="https://calendly.com/integrations/api_webhooks" target="_blank" rel="noreferrer">
                        <ExternalLink aria-hidden /> Open Calendly
                    </a>
                </Button>
            </div>
        );
    }

    if (!state.available) {
        return (
            <p className="text-sm text-muted-foreground">
                Calendly didn&apos;t respond. Check that the token in <code className="font-mono text-xs">.env</code> is
                still valid; this retries in a few minutes.
            </p>
        );
    }

    const byDay = new Map<string, Booking[]>();
    for (const booking of state.bookings) {
        const key = dayKey(new Date(booking.starts_at));
        byDay.set(key, [...(byDay.get(key) ?? []), booking]);
    }

    const today = new Date();
    const strip = Array.from({ length: DAYS }, (_, offset) => {
        const date = new Date(today.getFullYear(), today.getMonth(), today.getDate() + offset);

        return { date, count: byDay.get(dayKey(date))?.length ?? 0 };
    });

    return (
        <div className="space-y-5">
            <ol className="grid grid-cols-7 gap-1.5 sm:grid-cols-14" aria-label="Next two weeks">
                {strip.map(({ date, count }, index) => (
                    <li
                        key={date.toISOString()}
                        className={cn(
                            'flex flex-col items-center gap-1 rounded-md border py-2 text-xs',
                            index === 0 && 'border-primary',
                            count > 0 ? 'bg-primary/10' : 'bg-background',
                        )}
                    >
                        <span className="text-muted-foreground">{weekday.format(date)}</span>
                        <span className="font-medium tabular-nums">{date.getDate()}</span>
                        <span className={cn('size-1.5 rounded-full', count > 0 ? 'bg-primary' : 'bg-transparent')} />
                        <span className="sr-only">
                            {dayLabel.format(date)}: {count === 0 ? 'no calls' : `${count} call${count > 1 ? 's' : ''}`}
                        </span>
                    </li>
                ))}
            </ol>

            {state.bookings.length === 0 ? (
                <div className="rounded-lg border border-dashed p-6 text-center text-sm text-muted-foreground">
                    <CalendarClock className="mx-auto mb-2 size-5" aria-hidden />
                    No calls booked yet.
                    {!bookingUrl && ' Add your Calendly link in Settings so visitors can book one.'}
                </div>
            ) : (
                <div className="space-y-4">
                    {[...byDay.values()].map((bookings) => (
                        <div key={bookings[0].starts_at} className="space-y-2">
                            <h3 className="text-xs font-medium text-muted-foreground">
                                {dayLabel.format(new Date(bookings[0].starts_at))}
                            </h3>
                            <ul className="divide-y rounded-lg border">
                                {bookings.map((booking) => (
                                    <li
                                        key={booking.starts_at + booking.name}
                                        className="flex items-center gap-3 px-3 py-2.5"
                                    >
                                        <span className="w-28 shrink-0 font-mono text-xs tabular-nums">
                                            {time.format(new Date(booking.starts_at))}–
                                            {time.format(new Date(booking.ends_at))}
                                        </span>
                                        <div className="min-w-0 flex-1">
                                            <p className="truncate text-sm font-medium">
                                                {booking.invitee_name ?? 'Invitee'}
                                            </p>
                                            <p className="truncate text-xs text-muted-foreground">
                                                {booking.name}
                                                {booking.invitee_email && ` · ${booking.invitee_email}`}
                                            </p>
                                        </div>
                                        {booking.join_url && (
                                            <Button asChild variant="outline" size="sm">
                                                <a href={booking.join_url} target="_blank" rel="noreferrer">
                                                    <Video aria-hidden /> Join
                                                </a>
                                            </Button>
                                        )}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

export function UpcomingCallsSkeleton() {
    return (
        <div className="space-y-5" aria-busy="true" aria-label="Loading calendar">
            <div className="grid grid-cols-7 gap-1.5 sm:grid-cols-14">
                {Array.from({ length: DAYS }, (_, index) => (
                    <Skeleton key={index} className="h-16" />
                ))}
            </div>
            <div className="space-y-2">
                <Skeleton className="h-3 w-32" />
                <Skeleton className="h-12 w-full" />
                <Skeleton className="h-12 w-full" />
            </div>
        </div>
    );
}
