# Deploying to Vercel (free Hobby plan)

The site runs on Vercel's **Container Runtime**: Vercel builds `Dockerfile.vercel` and runs it as a function that scales
to zero when idle.

```
visitor → Vercel (HTTPS, CDN) → container: FrankenPHP (Laravel) + Node SSR server
                                     └→ Neon Postgres: content, cache, sessions, rate limits and uploads
```

- **No terminal needed.** Each new container runs `php artisan app:release`. It migrates under a database lock, so it
  is safe when several instances start at once, and on the very first release it creates your admin account and the
  real portfolio content.
- **Uploads** (résumé, images) are stored in the database and served from `/media/...`, because Vercel containers have
  no permanent disk.
- **Emails** are sent during the request (no background worker).
- **GitHub activity** is fetched when a page needs it, then cached.

| File | Purpose |
|---|---|
| `Dockerfile.vercel` | The image Vercel builds (FrankenPHP, assets, a self-contained SSR bundle) |
| `docker/vercel/Caddyfile` | FrankenPHP web server config |
| `docker/vercel/start.sh` | Starts SSR, runs `app:release`, then serves |
| `vercel.json` | Tells Vercel to use the container and send every path to it |

## Step 1. Put the code on GitHub

Commit and push this repository to GitHub (for example `github.com/cdrc1280/my-portfolio`). Private repositories work.

## Step 2. Create the Vercel project

1. Go to <https://vercel.com/signup> and sign up **with GitHub**. Choose the **Hobby** (free) plan.
2. Click **Add New… → Project**. Find `my-portfolio` and click **Import**. If it isn't listed, click **Adjust GitHub
   App Permissions** and allow access to the repository.
3. On the "Configure Project" screen, leave the settings as they are. Vercel reads `vercel.json` and builds the
   container.
4. **Don't click Deploy yet.** Add the environment variables first (step 4). If you already clicked it, that's fine:
   the first deploy simply fails, and you redeploy after step 4.

## Step 3. Add the database (one click)

1. In the project: **Storage** tab → **Create Database** → **Neon** (Serverless Postgres) → **Continue**.
2. Plan: **Free**. Region: **Singapore** (`ap-southeast-1`), or the one closest to you. Name it `portfolio-db`.
3. **Connect** it to the project for the **Production** and **Preview** environments.

This adds `DATABASE_URL` (and related variables) to the project automatically. You don't copy anything.

## Step 4. Environment variables

Project → **Settings → Environment Variables**. Add each of these for **Production**, plus **Preview** if you want
preview deployments to work:

| Name | Value |
|---|---|
| `APP_KEY` | The key in your local file `.env.vercel.local`, in the form `base64:…`. It's created for you and never committed. |
| `APP_URL` | `https://<your-project>.vercel.app`. The exact URL is shown on the project page after the first deploy; update it if it changes. |
| `APP_NAME` | `Cedric James Leala` |
| `ADMIN_NAME` | `Cedric James Leala` |
| `ADMIN_EMAIL` | The email you'll sign in to `/admin` with |
| `ADMIN_PASSWORD` | A strong password (only used when the admin is first created) |
| `CONTACT_NOTIFICATION_EMAIL` | Where contact-form briefs are sent |
| `GITHUB_USERNAME` | `cdrc1280` |

To actually send email (recommended), also add these. Gmail is free: Google Account → Security → 2-Step Verification →
**App passwords** → create one.

| Name | Value |
|---|---|
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` | `smtp.gmail.com` |
| `MAIL_PORT` | `587` |
| `MAIL_USERNAME` | your Gmail address |
| `MAIL_PASSWORD` | the 16-character app password, without spaces |
| `MAIL_FROM_ADDRESS` | your Gmail address |

Without these, emails are only written to the logs, and the contact form still works.

Everything else (database, cache, sessions, uploads, logging) has defaults in `Dockerfile.vercel`.

## Step 5. Deploy

**Deployments** tab → open the latest deployment → **⋯ → Redeploy**, or push any commit. The first build takes a
few minutes.

Open `https://<your-project>.vercel.app`. The first request after a quiet period can take a few seconds while a
container starts.

## Step 6. Finish in the admin

1. Go to `/admin` and sign in with `ADMIN_EMAIL` / `ADMIN_PASSWORD`.
2. **Settings:** upload your résumé PDF. Uploads must be under **4.5 MB**, Vercel's request size limit.
3. **Services:** remove the "test" service if it's there.
4. Send yourself a message from **Contact** to check that email works.

## Updating the site

Push to GitHub. Vercel rebuilds and deploys automatically, and new migrations run when the new containers start.
Pushes to other branches get their own preview URL.

## Custom domain (optional)

Project → **Settings → Domains → Add**. Vercel shows the DNS record to create at your registrar. Then update `APP_URL`
to the new domain and redeploy.

## Limits worth knowing (Hobby)

- **Cold starts:** after about 5 minutes without traffic the container stops, so the next visit waits a few seconds.
- **Uploads:** the request body limit is 4.5 MB per upload. Larger files are rejected with a clear error toast.
- **Database:** the Neon free tier has 0.5 GB of storage, plenty for this site including uploads.
- **Plan terms:** Hobby is for personal, non-commercial projects. A personal portfolio fits; if it ever becomes a
  business site, Vercel expects the Pro plan.
- **Container Runtime is in beta** on Vercel. If a deploy fails, the other host is documented in
  [oracle-cloud.md](oracle-cloud.md) and uses the regular `docker/php/Dockerfile` setup.

## Troubleshooting

| Symptom | Fix |
|---|---|
| Build fails | Deployments → the failed build → **Build Logs**. Paste the error to me if it's unclear. |
| "No application encryption key" | `APP_KEY` is missing or not prefixed with `base64:`. Add it and redeploy. |
| Errors mentioning the database | Check that Neon is connected to the project (**Storage** tab), then redeploy. The runtime logs show the `app:release` output. |
| Can't sign in to `/admin` | The admin is created only on the first release, with the env values at that moment. Check `ADMIN_EMAIL`, or reset the password from the Neon console. |
| Runtime errors | Project → **Logs** (runtime logs). Laravel logs go there because `LOG_CHANNEL=stderr`. |
