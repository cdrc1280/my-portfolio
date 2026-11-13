# Testimonials

Client quotes, kept hidden until you confirm the client agreed to publication.

- **Admin:** `/admin/testimonials` (sortable)
- **Public:** home page (up to 3 that are featured and publishable, in list order); a project page shows the first publishable quote linked to that project

## Fields

| Field | Rules |
| --- | --- |
| `author_name` | required, ≤100 |
| `author_role`, `company` | ≤100 |
| `quote` | required, ≤1500 |
| `project_id` | existing project, nullable |
| `has_permission` | bool. Without it the quote is never shown publicly (`publishable()` scope) |
| `is_featured` | bool |
| `photo` | image ≤1MB (`remove_photo`) |

## Admin side rail

Total, published (has permission), featured and hidden counts, plus tips.

## Code

- Controllers: `app/Domains/Testimonials/Controllers/Admin/TestimonialController.php` · routes: `routes/testimonials.php`
- Domain: `app/Domains/Testimonials`: `Data/TestimonialData`, CRUD and reorder actions, `Queries/TestimonialQueries` (admin list, featured for home, one per project), `Resources/{Testimonial,AdminTestimonial}Resource`
- Frontend: `components/custom/testimonials/*`, `pages/admin/testimonials/*` (page + its private components), `types/domains/testimonials.ts`

## Tests

`tests/Feature/Testimonials/Actions/*`, `tests/Feature/Testimonials/Admin/*`
