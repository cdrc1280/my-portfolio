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

/** ISO 8601 → `Nov 10, 2026` */
export function formatDate(iso: string): string {
    return dateFormatter.format(new Date(iso));
}

/** ISO 8601 → `Nov 10, 2026, 3:04 PM` */
export function formatDateTime(iso: string): string {
    return dateTimeFormatter.format(new Date(iso));
}
