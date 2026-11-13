# How I work

Working principles shown on the home page.

- **Admin:** `/admin/work-approaches` (sortable)
- **Public:** home page "How I work" cards, in list order. The grid is 2 columns, or 3 when the count is a multiple of 3

## Fields

`title` (required, ≤100), `description` (required, ≤600), `snippet` (≤200; shown in mono under the description), `icon` (≤60, alpha_dash).

## Admin side rail

Number of principles and how many have a snippet, plus tips.

## Code

- Controllers: `app/Domains/WorkApproach/Controllers/Admin/WorkApproachController.php` · routes: `routes/work-approach.php`
- Domain: `app/Domains/WorkApproach`: `Data/WorkApproachData`, CRUD and reorder actions, `Queries/WorkApproachQueries`, `Resources/WorkApproachResource`
- Frontend: `components/custom/work-approach/*`, `pages/admin/work-approaches/*` (page + its private components), `types/domains/work-approach.ts`

## Tests

`tests/Feature/WorkApproach/Actions/*`, `tests/Feature/WorkApproach/Admin/*`
