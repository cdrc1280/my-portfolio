# Operations

**Hosting:** Vercel (free Hobby plan, Container Runtime), step by step in [vercel.md](vercel.md). Self-hosting on any
Docker VM, for example Oracle Cloud's free tier, is in [oracle-cloud.md](oracle-cloud.md); it uses these scripts: `scripts/server-setup.sh` (one-time VM setup), `scripts/deploy.sh` (every deploy; `--seed` on the first),
`scripts/backup.sh` (daily database and uploads backup). Template: `.env.production.example`.

## Production build and deploy

`docker/php/Dockerfile` is a multi-stage build:

- `prod`: PHP-FPM with optimized vendor and compiled assets (also runs `queue` and `scheduler`)
- `ssr`: Node server that pre-renders Inertia pages (`INERTIA_SSR_URL`; the site falls back to client rendering if it is down)
- `web`: nginx serving `public/` and proxying to `app`

`docker-compose.prod.yml` adds Caddy in front for HTTPS (automatic Let's Encrypt for `DOMAIN`), plus MySQL and Redis
(both persisted in volumes). Laravel trusts the proxy's `X-Forwarded-*` headers (`bootstrap/app.php`).

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

This requires a real `.env` with `APP_ENV=production`, `APP_KEY`, `APP_URL`, `DB_*`, `DB_ROOT_PASSWORD` and `MAIL_*`.
`scripts/deploy.sh` runs migrations after the stack is up. Uploaded files live in the `storage` volume.


## Environment variables

| Variable | Purpose |
|---|---|
| `APP_ENV`, `APP_KEY`, `APP_URL` | Laravel basics; `APP_URL` is also the domain printed on share images |
| `DB_*`, `DB_ROOT_PASSWORD` | MySQL |
| `MAIL_*`, `CONTACT_NOTIFICATION_EMAIL` | Outgoing mail, and where contact-form briefs go |
| `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` | The admin account the seeder creates. Change them before deploying |
| `GITHUB_USERNAME`, `GITHUB_TOKEN` | GitHub activity tile (public repos; the token only raises the rate limit) |
| `CALENDLY_TOKEN` | Upcoming calls on the dashboard |
| `ANALYTICS_PROVIDER`, `ANALYTICS_SITE_ID`, `ANALYTICS_SCRIPT_URL` | Cookie-free analytics on public pages (Plausible or Umami); off when empty |

## After deploying a release

- `scripts/deploy.sh` runs the migrations.
- Upload the résumé PDF once in Settings (the seeder only attaches it from the gitignored `storage/app/seed/`).
- Cached shapes are versioned (`site.profile.v4`, `github.activity.v2.*`); bumping a key in code is enough when a cached
  object changes shape.
