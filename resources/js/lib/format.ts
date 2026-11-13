const monthFormatter = new Intl.DateTimeFormat('en', { month: 'short', year: 'numeric', timeZone: 'UTC' });
const dateFormatter = new Intl.DateTimeFormat('en', { dateStyle: 'medium' });
const dateTimeFormatter = new Intl.DateTimeFormat('en', { dateStyle: 'medium', timeStyle: 'short' });

/** `2023-07` → `Jul 2023` */
export function formatMonth(month: string): string {
    const [year, monthIndex] = month.split('-').map(Number);

    return monthFormatter.format(new Date(Date.UTC(year, monthIndex - 1, 1)));
}

/** `2023-07`, `null` → `Jul 2023 – Present` */
export function formatPeriod(start: string, end: string | null): string {
    return `${formatMonth(start)} – ${end ? formatMonth(end) : 'Present'}`;
}

/** `2018-06`, `2022-03` → `2018 – 2022`; null end → `2018 – Present`. Schooling is usually listed by year. */
export function formatYearRange(start: string, end: string | null): string {
    return `${start.slice(0, 4)} – ${end ? end.slice(0, 4) : 'Present'}`;
}

/** ISO 8601 → `Nov 10, 2026` */
export function formatDate(iso: string): string {
    return dateFormatter.format(new Date(iso));
}

/** ISO 8601 → `Nov 10, 2026, 3:04 PM` */
export function formatDateTime(iso: string): string {
    return dateTimeFormatter.format(new Date(iso));
}

/** `2024-01`, `2024-06` → `Jan 2024 – Jun 2024`; null end → `– Present`; null start → null. */
export function formatMonthRange(start: string | null, end: string | null): string | null {
    if (!start) {
        return end ? formatMonth(end) : null;
    }

    return formatPeriod(start, end);
}

/** `2026-11-12` → `Nov 12, 2026` (date only, no timezone shift). */
export function formatDay(day: string): string {
    const [year, month, date] = day.split('-').map(Number);

    return new Intl.DateTimeFormat('en', { dateStyle: 'medium', timeZone: 'UTC' }).format(
        new Date(Date.UTC(year, month - 1, date)),
    );
}
