# Uses

Tools and services for the `/uses` page.

- **Admin:** `/admin/uses` (sortable)
- **Public:** `/uses`. Grouped by category, in order of each category's first item; items keep the CMS order

## Fields

`category` (required, ≤60, free text; the dialog suggests existing categories), `name` (required, ≤100), `description` (≤500), `url` (http(s)).

## Admin side rail

Number of items and categories, how many have a link and how many have a description, items per category, and tips.

## Code

- Controllers: `app/Domains/Uses/Controllers/Admin/UsesItemController.php`, `app/Domains/Uses/Controllers/Public/UsesController.php` · routes: `routes/uses.php`
- Domain: `app/Domains/Uses`: `Data/UsesItemData`, CRUD and reorder actions, `Queries/UsesItemQueries`, `Resources/UsesItemResource`
- Frontend: `pages/admin/uses/*` (page + its private components), `types/domains/uses.ts`

## Tests

`tests/Feature/Uses/Actions/*`, `tests/Feature/Uses/Admin/*`, `tests/Feature/Uses/Public/*`
