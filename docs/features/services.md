# Services

What you offer, plus engagement and process settings.

- **Admin:** `/admin/services` (sortable list + "Engagement & process" form)
- **Public:** `/services` (all services, engagement types, starting rate, process steps); home page (first 4 services)

## Service fields

`title` (required, ≤100), `description` (required, ≤600), `icon` (≤60, alpha_dash; a Lucide icon name picked with `IconSelect`).

## Engagement settings

Saved on the profile row via `PUT /admin/services/settings` (`UpdateEngagementSettingsAction`):

| Field | Rules |
| --- | --- |
| `starting_rate` | ≤100 |
| `engagement_types[]` | ≤6 rows of `{title ≤100, description ≤300}` |
| `process_steps[]` | ≤8 rows of `{title ≤100, description ≤300}` |

Empty rows are ignored. Saving fires `ProfileChanged`, which clears the cached site profile.

## Seed data

`PortfolioSeeder::seedServices()` creates four services whose descriptions only restate résumé lines: Laravel web application development, Front-end development, APIs & MySQL databases, and Deployment & version control. It seeds no starting rate, engagement types or process steps; add those in the admin when they are real.

## Admin side rail

Number of services and process steps, starting rate, number of engagement types, and tips.

## Code

- Controllers: `app/Domains/Services/Controllers/Admin/ServiceController.php`, `app/Domains/Services/Controllers/Public/ServicesController.php` · routes: `routes/services.php`
- Domain: `app/Domains/Services`: `Data/ServiceData`, CRUD and reorder actions, `Queries/ServiceQueries`, `Resources/ServiceResource`. Engagement settings: `Profile/Data/EngagementSettingsData`, `Profile/Actions/UpdateEngagementSettingsAction`, `Profile/Resources/EngagementSettingsResource`
- Frontend: `components/custom/services/*`, `pages/admin/services/*` (page + its private components), `types/domains/services.ts`

## Tests

`tests/Feature/Services/Actions/*`, `tests/Feature/Services/Admin/*`, `tests/Feature/Services/Public/*`
