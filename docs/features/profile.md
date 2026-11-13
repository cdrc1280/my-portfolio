# Site settings (profile)

The single profile row behind the site: identity, bio, availability, contact, SEO defaults, files, feature switches and social links.

- **Admin:** `/admin/profile` (labelled "Settings" in the sidebar), `PUT /admin/profile`, social links (`/admin/social-links`: store, update, destroy, reorder), `PATCH /admin/availability` (from the dashboard)
- **Public:** every page via the shared `site` prop (`SiteProfileData`); `/about` (bio as Markdown), `/resume` (PDF download, 404 if none)

## Fields

| Group | Fields |
| --- | --- |
| Identity | `name` (req, ≤100), `headline` (req, ≤150), `tagline` (≤500), `avatar` (image), `location`, `timezone` |
| Bio | `short_bio` (≤500), `bio` (≤20000, Markdown) |
| Availability & contact | `availability_status` (`available` \| `limited` \| `unavailable`), `availability` note (≤150), `email`, `phone`, `working_hours`, `response_time`, `booking_url`, `currently_learning` |
| SEO & sharing | `seo_title` (≤70), `seo_description` (≤200), `og_image` |
| Files | `resume` (PDF ≤5MB, or under 4.5 MB on Vercel because of its request limit; sets `resume_updated_at`). Uploads go to `MEDIA_DISK` (the `database` disk on Vercel, served from `/media/...`) |
| Features | `blog_enabled`, `assistant_enabled` (see [assistant.md](assistant.md)) |
| Social links | `platform` (github, linkedin, upwork, onlinejobs, x, email, website, youtube, dribbble), `url` |

Every image and file field also has a `remove_*` flag.

## Behavior

- `GetProfileAction::snapshot()` caches `SiteProfileData` forever under `site.profile.v4`. Any profile, social link, availability or engagement change fires `ProfileChanged`, and `ForgetCachedProfile` clears the cache. **Bump the cache key when `SiteProfileData` changes shape.**
- The cache only unserializes allow-listed classes (see README gotchas).

## Admin side rail

A public card preview (avatar, name, headline, tagline, availability badge, link to the site) and a completeness checklist (avatar, bios, résumé, booking link, SEO, social image, social links).

## Code

- Controllers: `app/Domains/Profile/Controllers/Admin/AvailabilityController.php`, `app/Domains/Profile/Controllers/Admin/ProfileController.php`, `app/Domains/Profile/Controllers/Admin/SocialLinkController.php`, `app/Domains/Profile/Controllers/Public/AboutController.php`, `app/Domains/Profile/Controllers/Public/ResumeController.php` · routes: `routes/profile.php`
- Domain: `app/Domains/Profile`: `Data/{Profile,SocialLink,Availability,EngagementSettings}Data`, `Actions/*` (`GetProfileAction` creates and caches the single row), `Queries/SocialLinkQueries`, `Resources/{AdminProfile,AboutProfile,SocialLink,EngagementSettings}Resource`, `ValueObjects/SiteProfileData` (the cached shared prop)
- Frontend: `pages/admin/profile/*` (page + its private components), `types/domains/profile.ts`

## Tests

`tests/Feature/Profile/Actions/*`, `tests/Feature/Profile/Admin/*`, `tests/Feature/Profile/Public/*`
