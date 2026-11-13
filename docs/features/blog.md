# Blog

Markdown posts with drafts, scheduling and tags. The whole blog can be switched off.

- **Admin:** `/admin/posts`, `/admin/posts/create`, `/admin/posts/{id}/edit`
- **Public:** `/blog` (10 per page, `?tag=` filter), `/blog/{slug}`. Both return 404 when the blog is disabled (`EnsureBlogIsEnabled`, toggled by `blog_enabled` in [Site settings](profile.md))

## Fields

| Field | Rules | Notes |
| --- | --- | --- |
| `title` | required, ≤150 | |
| `slug` | required, ≤160, alpha_dash, unique | |
| `excerpt` | required, ≤500 | Card text |
| `body` | required, ≤100000, Markdown | Rendered server-side (`Markdown::toHtml`); reading time comes from it |
| `status` | `draft` \| `published` | |
| `published_at` | date, nullable | A future date on a published post = scheduled |
| `tag_ids[]` | existing tags | |
| `cover` | image ≤4MB (`remove_cover`) | |
| `seo_title` ≤70, `seo_description` ≤200 | | |

## Behavior

- Public queries use `published()`: status is published **and** `published_at <= now()`. Newest first.
- The admin edit page shows "View on site" only for live posts (published and not scheduled).
- Published posts are listed in the sitemap only while the blog is enabled.

## Code

- Controllers: `app/Domains/Blog/Controllers/Admin/PostController.php`, `app/Domains/Blog/Controllers/Public/BlogController.php`, `app/Domains/Blog/Controllers/Public/FeedController.php` · routes: `routes/blog.php`
- Domain: `app/Domains/Blog`: `Data/PostData`, `Actions/{Create,Update,Save,Delete}PostAction`, `Queries/PostQueries` (admin and public pagination via `Shared\Support\Paginated`), `Resources/{PostCard,Post,AdminPost}Resource`, `PostStatus`, `PostQueryBuilder`
- Frontend: `pages/public/blog/*` (page + its private components), `pages/admin/posts/*` (page + its private components), `types/domains/blog.ts`

## Tests

`tests/Feature/Blog/Actions/*`, `tests/Feature/Blog/Admin/*`, `tests/Feature/Blog/Public/*`
