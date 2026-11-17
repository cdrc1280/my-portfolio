# Architecture

## Backend: `app/Domains/<Name>/` and `app/Shared/`

Each domain owns its business logic. Every module has the same shape. **Tags is the reference module**,
so mirror it file for file when adding one.

```
app/Domains/Tags/
├── Controllers/   # Admin/ (CMS) and Public/ (site); thin, HasMiddleware + can:
├── Data/          # spatie/laravel-data input: rules(), prepareForPipeline() normalization, snake_case mapping
├── Actions/       # one write use case each, in DB::transaction, mapping Data → columns explicitly
├── Queries/       # the read side: {Thing}Queries (several reads) or {Thing}Query (one execute())
├── Resources/     # spatie Resource output with fromModel(); computed/display fields are built here
├── Models/        # Eloquent models (LogsModelActivity, #[UsePolicy], #[UseFactory], #[UseEloquentBuilder])
├── Enums/   Policies/   QueryBuilders/   Support/   ValueObjects/   Events/ Listeners/ Notifications/
```

Domains: `Account` (login settings), `Assistant`, `Blog`, `Bookings`, `Contact`, `Dashboard`, `Experience`
(work, education, certificates), `GitHub`, `Profile` (settings, social links, availability), `Projects`, `Seo`,
`Services`, `Tags` (polymorphic, shared by projects and posts; categorized tags are the skills), `Testimonials`,
`Uses`, `WorkApproach`, and `Site` (the Home page, which brings several modules together). `app/Shared` holds what more than one domain uses: `Data/ReorderData`,
`Exceptions/DomainRuleViolation`, `Concerns/LogsModelActivity` and `HasPosition`, `Support/Paginated`, `Input`,
`Markdown` and `MediaUrl`, the upload and reorder actions, and the base `AdminPolicy`.

Request flow (memorize this):

```
Route → Controller ─► Data (validates + normalizes input) ─► Action (writes, in a transaction)
                   └► Queries (reads) ─► Resource::fromModel() ─► Inertia::render('folder/page', props)
```

- **Controllers** live in their domain: `app/Domains/<Name>/Controllers/{Admin,Public}` (account settings use `Settings/`).
  `app/Http` keeps only the base `Controller`, middleware and Fortify wiring. Controllers are thin. They implement `HasMiddleware`
  and authorize with `can:` middleware backed by the domain policies. Dependencies are method-injected. They
  never build queries or contain business logic. Writes end with `$this->toast()` and a redirect.
- **No FormRequests, no JsonResources, no `$request->validate()`.** Input is a Data class type-hinted in the
  controller, which validates it automatically. Output is a Resource. Raw models never reach React. The only
  FormRequest left is Fortify's `TwoFactorAuthenticationRequest`, a two-factor state helper rather than input.
- **Business-rule failures** throw `DomainRuleViolation`, which `bootstrap/app.php` turns into an error toast
  and a redirect back. Field errors use normal validation.
- **Audit log:** every model uses `LogsModelActivity` (spatie/laravel-activitylog). It records changed
  attributes, skips no-op saves, and never stores hidden fields or `ip_hash`.
- **Uploads** go through one disk, `filesystems.media_disk` (`MEDIA_DISK`): `public` locally, or `database` on Vercel.
  The `database` disk (`App\Shared\Storage\DatabaseFilesystemAdapter`) keeps files in `stored_files`, and
  `App\Shared\Controllers\MediaController` serves them at `/media/<path>` (`routes/media.php`).
- **Releases:** `php artisan app:release` migrates under a Postgres advisory lock, seeds only when no user exists, and copies `ADMIN_EMAIL`/`ADMIN_PASSWORD` onto the admin when both are set.
  Vercel containers run it on start.
- **Databases:** MySQL locally and self-hosted, Postgres (Neon) on Vercel. The test suite passes on both, plus SQLite.
- **Paginated lists** go through `Shared\Support\Paginated::from()`, which keeps the `{ data, links, meta }`
  shape the React `Paginated<T>` type expects.
- **Listeners** in `app/Domains/*/Listeners` are auto-discovered.
- **Routes:** one file per module in `routes/<module>.php` (`site`, `profile`, `projects`, `blog`, `services`, `uses`,
  `contact`, `assistant`, `seo`, `dashboard`, `tags`, `experience`, `testimonials`, `work-approach`, `account`), all
  required from `routes/web.php`. Each file holds that module's public routes plus its admin routes inside the
  admin group (`auth` + `can:access-admin`, prefix `/admin`, names `admin.*`).

Deliberate differences from a generic back-office template: controllers inside a domain are split into `Admin/` and
`Public/`, because this app has a public site as well as a CMS. The site has one admin, so authorization uses policies
and the `access-admin` gate instead of a roles and permissions package. IDs are integers on MySQL, and the app is
not under `src/`.

## Frontend: `resources/js/`

```
app.tsx                      # Inertia app: layout by page name, error boundary, toasts, HTTP error fallback
pages/{public,admin,settings,auth}/
  admin/posts/index.tsx      # a page...
  admin/posts/post-form.tsx  # ...and the components only that page folder uses (spec: pages/{thing}/{thing}-form.tsx)
components/ui/               # shadcn/ui only; never hand-edited (regenerate with the shadcn CLI)
components/custom/           # app components: shared ones at the root (section, form-field, seo, …)
components/custom/<domain>/  # components several pages use, e.g. projects/project-grid.tsx
hooks/   layouts/   lib/
types/index.ts               # re-exports everything below
types/domains/<domain>.ts    # hand-written types mirroring the PHP Resources (snake_case)
routes/  actions/  wayfinder/  # generated by Wayfinder, gitignored
```

- **Where a component goes:** used by one page folder → next to those pages, imported relatively (`./post-form`). Used by
  several pages → `components/custom/<domain>/`. Used everywhere → the `components/custom/` root.
- **ESLint enforces it:** nothing imports through `@/pages`, and components, hooks, lib, types and layouts can't import
  from pages.
- **Types** mirror the Resources exactly (snake_case) and are imported from `@/types`.
- **Routes** are typed through [Wayfinder](https://github.com/laravel/wayfinder). URLs are never hard-coded. Vite
  regenerates them in dev; otherwise run `php artisan wayfinder:generate --with-form`.
- **Not used here:** the template's `schemas/`, `renderers/cells` and `lib/permissions.ts`. They configure data-table
  index pages and a roles system, and this app has neither.

## Security

- **Headers:** `app/Http/Middleware/SecurityHeaders.php` (web group) sets `X-Frame-Options`, `nosniff`, a Referrer-Policy, a Permissions-Policy that turns off camera, microphone and location, and a CSP limited to `base-uri`, `form-action`, `frame-ancestors` and `object-src`. Script and style sources stay open on purpose (inline theme script, Vite, optional analytics). HSTS comes from the edge (Vercel or Caddy).
- **Rate limits:** login allows 5 per minute per email and IP, plus 10 per minute per email from any IP. Two-factor allows 5 per minute; contact is limited per hour and the assistant to 20 per minute, both per IP.
- **Input that reaches HTML:** Markdown strips raw HTML and unsafe links (`Shared/Support/Markdown`). JSON-LD escapes `<`. The `appearance` cookie only passes `light`, `dark` or `system` into the page.
- **Files:** uploads are images (jpg, png, webp) or a PDF résumé, admin-only, and served from `/media` with `nosniff`. The private `local` disk is never served by URL (`serve` is off).
- **Accounts:** one admin, no registration; `is_admin` is not mass-assignable; secrets are hidden from serialization.
- **Known, accepted:** `npm audit` reports a `picomatch` ReDoS in build-time tooling (Vite plugins). No fixed version exists yet; it never runs in the browser or on the server at request time.

## Tests: `tests/`

Pest, grouped by domain: `tests/Feature/<Domain>/{Actions,Queries,Data,Admin,Public}/<Thing>Test.php`. For example,
`tests/Feature/Tags/Admin/TagControllerTest.php` covers the admin HTTP layer and `tests/Feature/Tags/Actions/*` covers
the write logic. Pure unit tests live in `tests/Unit/<Domain>/`, and cross-cutting ones in `tests/Feature/Shared/`.
Data classes need the container, so tests that build them live under `Feature`, not `Unit`.
Tests use SQLite in memory and a serializing array cache.
