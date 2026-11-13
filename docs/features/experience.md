# Experience

Work history, education and certificates, edited on one tabbed admin page.

- **Admin:** `/admin/experience` (tabs: Work / Education / Certificates; drag to reorder each)
- **Public:** `/about` (timeline, education, certificates), home page (first 3 roles)

## Work (`Experience`)

| Field | Rules | Notes |
| --- | --- | --- |
| `role` | required, ≤150 | |
| `company` | required unless confidential, ≤150 | Shown publicly as "Confidential client" when `is_confidential` |
| `company_url` | http(s) URL | |
| `is_confidential`, `is_remote` | bool | |
| `location` | ≤150 | |
| `employment_type` | optional: `full_time` \| `part_time` \| `contract` \| `freelance` \| `internship` | Leave empty when not stated, so the timeline never shows a guessed type |
| `started_on`, `ended_on` | `Y-m`, end ≥ start | No end = current role ("Present") |
| `summary` | ≤2000 | |
| `highlights[]` | ≤12 × ≤300 | |
| `technologies[]` | ≤20 × ≤50 | |

## Education

`institution`, `qualification` (required, ≤150), `field_of_study` (≤150), `started_on` / `ended_on` (`Y-m`), `notes` (≤1000). Shown as **years only** (`formatYearRange`), since schooling is listed by year.

## Certificates

`name` (required, ≤150), `issuer` (optional, ≤150), `issued_on` (`Y-m`), `credential_url` (http(s)), `badge` (image ≤1MB, `remove_badge`).

## Admin side rail

Career at a glance (years since the earliest start, role / education / certificate counts, current role, remote and confidential counts), the most-used technologies across roles, and tips.

## Code

- Controllers: `app/Domains/Experience/Controllers/Admin/CertificateController.php`, `app/Domains/Experience/Controllers/Admin/EducationController.php`, `app/Domains/Experience/Controllers/Admin/ExperienceController.php` · routes: `routes/experience.php`
- Domain: `app/Domains/Experience`: `Data/{Experience,Education,Certificate}Data` (months cast from `YYYY-MM`), CRUD actions per model, `ReorderCareerHistoryAction`, `Queries/ExperienceQueries`, `Resources/{Experience,AdminExperience,Education,Certificate}Resource` (the public resource hides confidential clients), `EmploymentType`
- Frontend: `components/custom/experience/*`, `pages/admin/experience/*` (page + its private components), `types/domains/experience.ts`

## Tests

`tests/Feature/Experience/Actions/*`, `tests/Feature/Experience/Admin/*`, `tests/Unit/Experience/*`
