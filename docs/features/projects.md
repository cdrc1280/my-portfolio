# Projects

Portfolio case studies. The most detailed content type on the site.

- **Admin:** `/admin/projects` (list, drag to reorder), `/admin/projects/create`, `/admin/projects/{id}/edit`
- **Public:** `/projects` (grid with tag filter), `/projects/{slug}` (case study), up to three featured projects on the home page, `/sitemap.xml`

## Fields

| Field | Rules | Notes |
| --- | --- | --- |
| `title` | required, ≤150 | |
| `slug` | required, ≤160, alpha_dash, unique | URL |
| `summary` | required, ≤200 | Card text |
| `problem`, `role_details`, `results` | ≤5000, Markdown | Case study sections |
| `role` | ≤150 | |
| `architecture` | ≤20000, Markdown | |
| `architecture_tree` | ≤10000 | Plain-text folder listing, parsed by `FolderTree` into a tree |
| `decisions`, `challenges` | ≤10000, Markdown | |
| `metrics[]` | ≤8 rows of `{label ≤60, value ≤30}` | |
| `live_url`, `repo_url`, `demo_url` | http(s) URL | |
| `is_confidential` | bool | |
| `started_on`, `ended_on` | `Y-m`, end ≥ start | |
| `seo_title` ≤70, `seo_description` ≤200 | | |
| `status` | `draft` \| `published` | Only published projects are public |
| `is_featured` | bool | Home page shows the first 3 featured, in list order |
| `tag_ids[]` | existing tags | |
| `cover`, `diagram` | image (`remove_*` to clear) | |
| `images[]` | ≤10 images; `removed_image_ids[]` | Gallery (`ProjectImage`) |

## Behavior

- **Case study layout** (`public/projects/show.tsx`): sections run problem → my role → architecture → key decisions → challenges → results → gallery → client feedback, and empty ones are skipped. Each section is a `surface` panel (`Block`) so text never sits on the dot field. On `lg` and up the section label and title sit in a sticky left column. A row of jump links (`SectionNav`) under the header lists the sections that are present.
- Each case study has a generated share card (`/og/projects/{slug}.png`) and `CreativeWork` JSON-LD (see [seo.md](seo.md)). The "Live site" button fires the `Project live site` analytics event.
- The list order is the public order (`ReorderProjectsAction`).
- The case study page shows the project's first publishable testimonial and a "next project" link (`NextProjectQuery`).
- The public tag filter only lists tags that have at least one published project.
- Deleting a project also removes its stored files (`DeleteProjectAction`, `SyncProjectMediaAction`).

## Code

- Controllers: `app/Domains/Projects/Controllers/Admin/ProjectController.php`, `app/Domains/Projects/Controllers/Public/ProjectController.php` · routes: `routes/projects.php`
- Domain: `app/Domains/Projects`: `Data/ProjectData` (raw folder-tree text with `folderTree()`, `completeMetrics()`), `Actions/*` (create, update, delete, reorder, `SyncProjectMediaAction`), `Queries/ProjectQueries` and `NextProjectQuery`, `Resources/{ProjectCard,Project,AdminProject}Resource`, `ProjectStatus`, `ProjectQueryBuilder`, `Support/FolderTree.php`
- Frontend: `components/custom/projects/*`, `pages/admin/projects/*` (page + its private components), `pages/public/projects/*` (page + its private components), `types/domains/projects.ts`, `hooks/use-project-form.ts`

## Tests

`tests/Feature/Projects/Actions/*`, `tests/Feature/Projects/Admin/*`, `tests/Feature/Projects/Public/*`, `tests/Feature/Projects/Queries/*`, `tests/Unit/Projects/*`
