# Portfolio

A personal portfolio and blog with a built-in admin CMS. It is built on Laravel 13, Inertia 3, React 19, TypeScript and
Tailwind 4, and the backend is organized by domain (DDD-style).

Public site: home, projects (with tag filter), blog, about, and contact.
Admin (`/admin`): projects, tags, skills, experience/education/certificates, blog posts, site profile, and the
contact inbox. Drag-to-reorder, image uploads, Markdown content, and post drafts/scheduling are supported.

## Requirements

- Docker with Compose v2
- `make`

Docker is the only supported runtime. PHP, Composer and Node run inside containers.

## Getting started

```bash
make setup      # copies .env, builds images, installs deps, generates key, migrates + seeds
make up         # start the stack
```

| Service | URL |
|---|---|
| App | http://localhost:8000 |
| Vite dev server | http://localhost:5173 (`VITE_PORT`) |
| Mailpit | http://localhost:8025 (`FORWARD_MAILPIT_DASHBOARD_PORT`) |

The seeded admin account comes from `ADMIN_NAME` / `ADMIN_EMAIL` / `ADMIN_PASSWORD` in `.env`
(the defaults are `admin@example.com` / `password`). Change these before you deploy.

### Everyday commands

```bash
make help       # list every target
make test       # Pest (parallel)
make lint       # Pint, Larastan (level 7), ESLint, Prettier, tsc
make fix        # auto-fix style
make ci         # lint + test, the same gates CI runs
make fresh      # migrate:fresh --seed
make shell      # shell in the app container
make logs       # tail all services
```

### Environment variables worth knowing

| Variable | Purpose |
|---|---|
| `ADMIN_*` | Seeded admin user |
| `CONTACT_NOTIFICATION_EMAIL` | Where contact-form messages are emailed |
| `GITHUB_USERNAME`, `GITHUB_TOKEN` | GitHub activity section (public repos only; the token is optional and raises the rate limit) |
| `VITE_PORT`, `FORWARD_MAILPIT_DASHBOARD_PORT` | Change these if the default ports are already in use |

## Services

The dev stack (`docker-compose.yml`) runs `app` (PHP-FPM), `nginx`, `queue` (worker), `scheduler`, `node` (Vite), `db`
(MySQL 8.4), `redis` and `mailpit`.

The scheduler runs `github:refresh` hourly, and contact-form emails are sent through the queue.

## Production

`docker/php/Dockerfile` is a multi-stage build:

- `prod`: PHP-FPM with optimized vendor and compiled assets
- `web`: nginx serving `public/` and proxying to `app`

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

This requires a real `.env` with `APP_ENV=production`, `APP_KEY`, `APP_URL`, `DB_*`, `DB_ROOT_PASSWORD` and `MAIL_*`.
The `app` container runs migrations on start (`RUN_MIGRATIONS=true`). Uploaded files live in the `storage` volume.

## Architecture

### Backend: `app/Domain/<Name>/`

Each domain owns its business logic:

```
app/Domain/Projects/
├── Actions/               # one class per use case (CreateProject, ReorderProjects, ...)
├── DataTransferObjects/   # typed input built from FormRequests
├── Enums/
├── Models/                # Eloquent models, wired with attributes
├── Policies/              # extend Shared\Policies\AdminPolicy
└── QueryBuilders/         # custom Eloquent builders (published(), ordered(), ...)
```

Domains: `Blog`, `Contact`, `Dashboard`, `Experience` (work, education, certificates), `GitHub`, `Profile`, `Projects`,
`Seo`, `Skills`, `Tags` (polymorphic, shared by projects and posts), and `Shared` (reordering, uploads, Markdown, media
URLs, base policy).

The HTTP layer stays thin:

- **Controllers** (`app/Http/Controllers/{Admin,Public}`) always follow the same flow: FormRequest, then `->toData()`
  (DTO), then Action, then an Inertia response or a redirect with `$this->toast()`.
- **Resources** (`app/Http/Resources`, with `Admin/` variants) shape every prop. Raw models are never passed to Inertia.
- **Models** declare their wiring with `#[UsePolicy]`, `#[UseFactory]` and `#[UseEloquentBuilder]`.
- **Listeners** in `app/Domain/*/Listeners` are auto-discovered.
- **Routes**: public routes are in `routes/web.php` and admin routes in `routes/admin.php` (behind the `is_admin` gate).

### Frontend: `resources/js/`

```
pages/{public,admin}/   # thin Inertia pages that compose features
features/<name>/        # components, hooks, types.ts mirroring the PHP Resources
components/shared/      # app-wide components
components/ui/          # shadcn/ui only
layouts/  hooks/  lib/  types/
```

- An ESLint rule blocks imports from one feature into another. Code needed by more than one feature moves to `components/shared`.
- Routes are typed through [Wayfinder](https://github.com/laravel/wayfinder). `resources/js/{routes,actions,wayfinder}` are
  generated and gitignored. Vite regenerates them in dev. Otherwise run
  `php artisan wayfinder:generate --with-form`.

### Tests: `tests/`

Pest. Domain logic is tested in `tests/Feature/Domain/<Name>/`, and HTTP is tested in `tests/Feature/Http/{Admin,Public}/`.
Tests use SQLite in memory and a serializing array cache.

## Adding a domain module

1. Migration in `database/migrations`
2. Model and factory in `app/Domain/<Name>/Models` (add the `#[UseFactory]` / `#[UsePolicy]` attributes)
3. DTO in `DataTransferObjects/`
4. Actions in `Actions/`
5. Policy extending `AdminPolicy`
6. `Store`/`Update` FormRequests with a `toData()` method
7. Resource(s) in `app/Http/Resources` (plus an `Admin/` variant if needed)
8. Controller and routes in `routes/admin.php` / `routes/web.php`
9. `php artisan wayfinder:generate --with-form`
10. `resources/js/features/<name>/` (components, `types.ts`) and the pages
11. Sidebar entry in `resources/js/components/app-sidebar.tsx`
12. Tests in `tests/Feature/Domain/<Name>/` and `tests/Feature/Http/{Admin,Public}/`
13. Seed data, if needed, in `database/seeders`

## Gotchas

- **Cached objects:** Laravel 13 only unserializes allowlisted classes. Add any class you cache to
  `config/cache.php` → `serializable_classes`. The test cache store serializes values, so it catches a missing entry.
- **No `env_file` on the dev `app` service:** Docker env vars override `phpunit.xml`, which would point tests at MySQL.
- **nginx config:** the config **directory** is mounted, not a single file. A single-file bind mount keeps serving stale config after edits.
- **Markdown** is rendered server-side with CommonMark and raw HTML is stripped.
