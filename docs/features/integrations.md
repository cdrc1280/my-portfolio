# Integrations

## GitHub activity

- Dashboard tile (and public use where shown). `GetGitHubActivityAction` reads the cache (`github.activity.v2.{username}`) and refreshes on a miss.
- Success is cached for `portfolio.github.cache_minutes` (default 60). A failure caches `false` for 5 minutes. Returns `null` when no username is configured.
- Code: `app/Domains/GitHub` (`GitHubClient`, `FetchGitHubActivityAction`, `ValueObjects/GitHubActivityData`), `components/custom/github/github-activity.tsx`
- Tests: `tests/Feature/GitHub/Actions/*`

## Calendly (upcoming calls)

- Dashboard "Upcoming calls" tile. `GetUpcomingBookingsAction` caches plain arrays (not DTOs) under `calendly.upcoming.v1` for `portfolio.calendly.cache_minutes` (default 10). A failure is cached for 5 minutes. It returns `configured: false` when there is no token.
- Code: `app/Domains/Bookings` (`CalendlyClient`, `FetchUpcomingBookingsAction`, `ValueObjects/BookingData`), `components/custom/dashboard/upcoming-calls.tsx`
- Tests: `tests/Feature/Bookings/Actions/*`

## Analytics (Plausible or Umami)

- Off until `ANALYTICS_PROVIDER` (`plausible` | `umami`) and `ANALYTICS_SITE_ID` are set. Umami also needs `ANALYTICS_SCRIPT_URL`. Config: `portfolio.analytics`.
- The script is included from `resources/views/partials/analytics.blade.php` on public pages only: never `/admin`, `/settings` or the auth pages. Both tools are cookie-free and follow Inertia navigation automatically.
- Custom events go through `track()` in `resources/js/lib/analytics.ts`, which does nothing when no script is loaded: `Contact brief sent` (project type, currency), `Book a call`, `Résumé download` and `Project live site` (project).
- Tests: `tests/Feature/Site/Public/AnalyticsAndNavigationTest.php`
