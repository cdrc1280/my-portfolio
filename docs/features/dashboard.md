# Dashboard

The admin landing page: calls, leads, availability, content health, counts, inbox and GitHub, laid out as a bento grid.

- **Admin:** `GET /admin` → `admin.dashboard` → `resources/js/pages/admin/dashboard.tsx`
- **Public:** none

## Tiles

| Tile | Source | Notes |
| --- | --- | --- |
| Greeting + quick actions | shared `site` prop | "Post" and "Project" create buttons |
| Upcoming calls | `GetUpcomingBookingsAction` (Calendly) | Deferred prop; hidden or shows a notice when Calendly isn't configured or is unavailable |
| Availability | `AvailabilityControl` → `PATCH /admin/availability` | Changes the public badge without leaving the page (`UpdateAvailabilityAction`) |
| Leads | `LeadStatsQuery` | Unread, this week vs last week, and the budget (USD equivalent) / project-type mix over the last 90 days |
| Content health | `ContentHealthQuery` | Checklist: screenshots on published projects, complete case studies (problem / architecture / results), three featured projects, testimonials, résumé no older than 180 days, … Each item links to where it is fixed |
| Counts | `DashboardStatsQuery` | Projects, posts, messages, tags |
| Recent messages | `MessageList` | |
| GitHub activity | `GetGitHubActivityAction` | Deferred prop; `null` when not configured. See [integrations.md](integrations.md) |

## Code

- Controllers: `app/Domains/Dashboard/Controllers/Admin/DashboardController.php` · routes: `routes/dashboard.php`
- Domain: `app/Domains/Dashboard/Queries/{DashboardStatsQuery,LeadStatsQuery,ContentHealthQuery}`, `ValueObjects/DashboardStatsData`. The calls and GitHub tiles use the Bookings and GitHub actions, which do HTTP and caching.
- Frontend: `components/custom/dashboard/*`, `components/custom/github/*`, `types/domains/dashboard.ts`, `types/domains/github.ts`

## Tests

`tests/Feature/Dashboard/Admin/*`, `tests/Feature/Dashboard/Queries/*`
