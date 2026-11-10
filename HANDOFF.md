# Handoff: portfolio rebuild

Status as of 2026-11-11. The migration from Vue + Filament to Laravel 13 + Inertia + React + TS (DDD) is
**mostly done**. What remains: finish Phase 4 (commit the cleanup), confirm the production Docker image
builds, and write the README.

## Where things stand

### Committed on `main` (11 commits, all checks green at each one)

| Commit | What |
|---|---|
| `a60f5c4` | Baseline: official React starter kit, old app moved to `legacy/`, Pest, ESLint + Prettier |
| `f4d9a18` | Docker: dev compose, multi-stage Dockerfile, `Makefile` |
| `fae0994` | Shared domain (reorder, uploads, Markdown, MediaUrl, AdminPolicy, `is_admin` gate), frontend shell |
| `b05cbf0` | Projects + Tags |
| `e0db43a` | Skills |
| `771e4a2` | Experience (work, education, certificates) |
| `d794f99` | Profile (site identity, socials, résumé download, cached `site` prop) |
| `a4fc154` | Contact (honeypot, rate limit, queued email, admin inbox) |
| `89785e0` | Blog (drafts, scheduling, tags) |
| `ab4a713` | Home, About, GitHub activity (deferred prop), dashboard, sitemap.xml, robots.txt |
| `98f6669` | Seeders with your real content, plus a cache-unserialization fix |

At the last run there were **218 Pest tests passing**, and Pint, Larastan (level 7), ESLint, Prettier and
`tsc` were all clean. Smoke tests on the running stack: every public and admin page returned 200, with no errors in `laravel.log`.

### Phase 4 cleanup (committed and pushed with this handoff)

- `legacy/` deleted (174 tracked files: `git rm -r`, staged). Everything in it was ported or intentionally dropped.
- References to `legacy` removed from `pint.json`, `.prettierignore`, `.dockerignore`, `eslint.config.js`,
  `vite.config.ts` and `docker/php/Dockerfile`.
- `.github/workflows/tests.yml` rewritten: a `quality` job (Pint, Larastan, ESLint, Prettier, tsc, Pest, build)
  and a `docker` job (builds the `prod` and `web` targets).
- `composer.json`: package renamed to `sedistasis/portfolio`; removed the kit's `setup`/`dev` scripts (Docker + `make`
  is the only supported workflow); `composer.lock` hash refreshed (no dependency changes).

Verified before pushing: Pint, Larastan, ESLint, Prettier and tsc are clean, and 218 Pest tests pass.

## To do next (in order)

1. **Check the first CI run on GitHub.** This is the first push of the new workflow.
2. **Build the production images.** I stopped this mid-run; it has never completed:
   ```bash
   docker build -f docker/php/Dockerfile --target prod -t portfolio-app .
   docker build -f docker/php/Dockerfile --target web  -t portfolio-web .
   ```
   Points to watch:
   - The `assets` stage runs `php artisan` (Wayfinder) during `npm run build`, using a throwaway `.env`.
   - The `vendor` stage uses `--ignore-platform-reqs`.
   - If both builds succeed, optionally try `docker compose -f docker-compose.prod.yml up` with a real `.env`.
3. **Write `README.md`**. The spec asks for: setup, architecture overview, folder conventions, and how to add a domain
   module. The README is still the starter kit's (or absent). Key facts to include are in "Conventions" below.
4. **Browser check (recommended).** Only HTTP status codes and server logs have been checked, so client-side React
   errors haven't been ruled out. Open the site in a browser and click through: home, projects (tag filter), project
   detail, blog, about, contact (submit, then check Mailpit), and every admin page (create/edit/reorder/upload/delete).
5. Optional follow-ups:
   - `npm audit` reports 7 moderate dev-only advisories in the typescript-eslint chain.
   - The Vite fonts plugin suggests installing `fontaine`.
   - Replace the kit's Laravel logo (`app-logo-icon.tsx`) with your own mark.

## Running it

```bash
make up         # app :8000, Vite :5174 (your .env), Mailpit :8026 (your .env)
make test       # Pest (parallel)
make lint       # Pint, Larastan, ESLint, Prettier, tsc
make fix        # auto-fix style
make fresh      # migrate:fresh --seed
```

Admin login: `admin@example.com` / `password` (set by `ADMIN_*` in `.env`). Admin lives at `/admin`.

Your local `.env` uses **VITE_PORT=5174** and **FORWARD_MAILPIT_DASHBOARD_PORT=8026**, because another stack on this
machine (cloutia) holds 5173 and 8025. `.env.example` keeps the conventional defaults.

## Conventions

- **Backend** is organized per domain in `app/Domain/<Name>/`: Actions, DataTransferObjects, Models, Enums, Policies,
  QueryBuilders, Events, Listeners.
  - Controllers follow one pattern: FormRequest, then `->toData()` (DTO), then Action, then Inertia response or redirect plus `$this->toast()`.
  - Models declare their wiring with `#[UsePolicy]`, `#[UseFactory]` and `#[UseEloquentBuilder]`.
  - Listeners in `app/Domain/*/Listeners` are auto-discovered (`bootstrap/app.php`).
  - Props always go through Resources (`app/Http/Resources`, plus `Admin/` variants), never raw models.
- **Frontend** lives in `resources/js`:
  - `pages/{public,admin}`: thin pages only.
  - `features/<name>`: components, hooks, `types.ts` mirroring the PHP Resources.
  - `components/shared`: app-wide components.
  - `components/ui`: shadcn only.
  - An ESLint rule blocks feature-to-feature imports, so shared code moves to `components/shared`.
- **Adding a domain module:**
  1. Migration
  2. Model + factory
  3. DTO
  4. Actions
  5. Policy (extend `AdminPolicy`)
  6. Store/Update requests
  7. Resource(s)
  8. Controller + routes (`routes/admin.php` / `routes/web.php`)
  9. `php artisan wayfinder:generate --with-form`
  10. `features/<name>` + pages
  11. Sidebar entry (`components/app-sidebar.tsx`)
  12. Tests in `tests/Feature/Domain/<Name>/` and `tests/Feature/Http/{Admin,Public}/`

## Decisions made along the way

- Drag-to-reorder uses `@dnd-kit` (keyboard accessible). Markdown is rendered server-side with CommonMark, with raw HTML stripped.
- Education and certificates live in the Experience domain; Tags are shared by projects and posts (polymorphic).
- GitHub stats use **public repos only**. They are cached, with failures cached briefly, refreshed hourly by
  `github:refresh` (the `scheduler` service), and loaded as a deferred prop.
- Flagged rather than changed: the seeded profile includes your street address and phone number, as you approved.
  The phone number is never sent to public pages; the address appears as "location" on About and Contact.

## Gotchas found and fixed (don't reintroduce)

- **Cache serialization:** Laravel 13 refuses to unserialize cached objects that aren't allowlisted. Any new class you cache must be added to
  `config/cache.php` → `serializable_classes`. Tests use a serializing array store, so they catch this.
- **Docker env files:** `env_file` must not be used for the dev `app` service. Docker env vars override `phpunit.xml`, which sends tests to MySQL.
- **nginx config edits:** nginx mounts the config **directory**. A single-file bind mount keeps serving stale config after edits.
- **Wayfinder output:** `resources/js/{routes,actions,wayfinder}` are generated and gitignored. Run `wayfinder:generate` after route
  changes (Vite does this automatically in dev).
