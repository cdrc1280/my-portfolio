# Tags & skills

Tags label projects and posts. A tag with a category also appears as a skill on the home page.

- **Admin:** `/admin/tags` (sortable; the order is the skill order)
- **Public:** project and blog tag filters and badges; home page skill groups (`SkillGroupsQuery`, grouped in `TagCategory` order)

## Fields

| Field | Rules |
| --- | --- |
| `name` | required, ≤50 |
| `slug` | required, ≤60, alpha_dash |
| `category` | nullable: `backend` \| `frontend` \| `database` \| `devops_tools` \| `testing` \| `ai_tools` |
| `icon` | ≤60, alpha_dash |

## Admin side rail

Number of tags, skills, unused tags (no projects or posts) and categories; skills per category; most-used tags.

## Code

- Controllers: `app/Domains/Tags/Controllers/Admin/TagController.php` · routes: `routes/tags.php`
- Domain: `app/Domains/Tags`, **the reference module**: `Data/TagData` (slug derived from the name, with uniqueness reported on `name`), `Actions/{Create,Update,Delete,Reorder}…`, `Queries/TagQueries` and `SkillGroupsQuery`, `Resources/{Tag,AdminTag}Resource`, `TagCategory`
- Frontend: `components/custom/tags/*`, `pages/admin/tags/*` (page + its private components), `types/domains/tags.ts`

## Tests

`tests/Feature/Tags/Actions/*`, `tests/Feature/Tags/Admin/*`, `tests/Feature/Tags/Queries/*`
