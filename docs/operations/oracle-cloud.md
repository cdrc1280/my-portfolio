# Deploying to Oracle Cloud (Always Free)

The site runs on one free Oracle Cloud VM with the Docker stack in `docker-compose.prod.yml`:

```
internet → caddy (HTTPS :80/:443) → web (nginx) → app (PHP-FPM) ↔ ssr (Node pre-rendering)
                                                   ↘ queue + scheduler (same image)   db (MySQL) · redis
```

Caddy gets and renews the HTTPS certificate automatically. `scripts/deploy.sh` runs the migrations on every deploy.

You do steps 1 to 4 once in the browser. Steps 5 to 8 are commands on the server.

## 1. Create the account

Sign up at <https://www.oracle.com/cloud/free/>. Oracle asks for a card to verify you, but nothing is charged while you
stay within the Always Free resources below.

- **Home region:** pick the one closest to you or your clients, for example Singapore or Tokyo for the Philippines. It
  can't be changed later, and Always Free Arm capacity varies by region.
- Stay on the **Free Tier**. Don't upgrade to "Pay As You Go" unless you want to; even then, Always Free resources stay
  free.

## 2. Create the VM

Console → **Compute → Instances → Create instance**:

| Setting | Value |
|---|---|
| Image | **Canonical Ubuntu 24.04** |
| Shape | **Ampere → VM.Standard.A1.Flex**, **2 OCPU, 12 GB** memory (the Always Free allowance) |
| Networking | Create a new VCN and public subnet, with **Assign a public IPv4 address** on |
| SSH keys | **Generate a key pair** and download the private key, or paste your own public key |
| Boot volume | 50 GB is plenty (the Always Free storage total is 200 GB) |

If you get "Out of capacity" for the Arm shape, try another availability domain, or try again later. Arm capacity
frees up during the day.

Note the instance's **Public IP address**.

## 3. Open ports 80 and 443 in Oracle's network

Instance page → the **subnet** link → its **Security List** → **Add Ingress Rules**. Add two rules:

| Source CIDR | IP protocol | Destination port |
|---|---|---|
| `0.0.0.0/0` | TCP | `80` |
| `0.0.0.0/0` | TCP | `443` |

SSH (22) is already open. The server has its own firewall too; `scripts/server-setup.sh` opens these ports there in
step 5.

## 4. Point a domain at the server

HTTPS needs a domain name. Either option works:

- **Free:** create a subdomain at <https://www.duckdns.org> (sign in with GitHub or Google), for example
  `cedric.duckdns.org`, and set its IP to the VM's public IP.
- **Your own domain:** add an **A record** for `@` (or `www`) pointing to the public IP at your registrar.

DNS can take a few minutes. Check it with `nslookup cedric.duckdns.org`.

## 5. Prepare the server

```bash
ssh -i path/to/private.key ubuntu@<PUBLIC_IP>

git clone https://github.com/cdrc1280/my-portfolio.git
cd my-portfolio
bash scripts/server-setup.sh
exit   # log out and back in so Docker works without sudo
```

If the GitHub repository is private, create a **fine-grained personal access token** with read access to this one
repository (GitHub → Settings → Developer settings) and use it as the password when `git clone` asks.

## 6. Configure the environment

```bash
ssh -i path/to/private.key ubuntu@<PUBLIC_IP>
cd my-portfolio
cp .env.production.example .env
nano .env
```

Replace every `CHANGE` value:

- **`APP_URL` and `DOMAIN`:** your domain from step 4. `APP_URL` includes `https://`; `DOMAIN` doesn't.
- **`DB_PASSWORD`, `DB_ROOT_PASSWORD`:** long random strings. Generate them with `openssl rand -base64 24`.
- **`ADMIN_EMAIL`, `ADMIN_PASSWORD`:** your login for `/admin`. Use a strong password.
- **`MAIL_*` and `CONTACT_NOTIFICATION_EMAIL`:** for the contact form. The free option is Gmail with an **app
  password** (Google Account → Security → 2-Step Verification → App passwords).

Leave `APP_KEY` empty; the deploy script generates it.

## 7. First deploy

```bash
bash scripts/deploy.sh --seed
```

The first build takes about 5–10 minutes on the free Arm VM. `--seed` creates the admin account and the real portfolio
content, and is only for this first run. When it finishes, open `https://<your domain>`. Caddy fetches the certificate
on the first request, so give it a few seconds.

Then sign in at `https://<your domain>/admin` and:

1. **Settings:** upload your résumé PDF (the seeder can't, because the file isn't in git).
2. **Services:** delete the "test" service if it's still there.

## 8. Backups (recommended)

```bash
bash scripts/backup.sh   # writes ~/backups/db_*.sql.gz and storage_*.tar.gz, keeps 14 days
(crontab -l 2>/dev/null; echo "30 3 * * * bash $HOME/my-portfolio/scripts/backup.sh >> $HOME/backups/backup.log 2>&1") | crontab -
```

Copy a backup off the server now and then, for example `scp ubuntu@<PUBLIC_IP>:backups/db_*.sql.gz .`.

## Updating the site

Push your changes to GitHub, then on the server:

```bash
cd my-portfolio && bash scripts/deploy.sh
```

It pulls, rebuilds, restarts once MySQL is healthy, runs migrations, clears caches, restarts the queue worker and prunes
old images.

## Day-to-day commands

```bash
cd my-portfolio
docker compose -f docker-compose.prod.yml ps              # what's running
docker compose -f docker-compose.prod.yml logs -f app     # PHP / Laravel logs (also: caddy, queue, ssr, web)
docker compose -f docker-compose.prod.yml exec app php artisan tinker
docker compose -f docker-compose.prod.yml restart         # restart everything
```

## Troubleshooting

| Symptom | Fix |
|---|---|
| Browser can't connect at all | Check the security list ingress rules (step 3) and run `sudo iptables -L INPUT -n --line-numbers`: 80/443 must be ACCEPTed before the REJECT rule. Re-run `scripts/server-setup.sh`. |
| Certificate error | The domain must resolve to the VM's IP *before* Caddy starts. Check `docker compose -f docker-compose.prod.yml logs caddy`, then `restart caddy`. |
| 500 errors | `docker compose -f docker-compose.prod.yml logs app`. Usually a `.env` value. After editing `.env`, run `bash scripts/deploy.sh` again so the config cache is rebuilt. |
| Contact emails not arriving | `logs queue`. Check the `MAIL_*` values and the Gmail app password. |
| Pages work but look unstyled for crawlers | The `ssr` container is down: `logs ssr`. The site still works for visitors (client-side rendering). |
| "Out of capacity" when creating the VM | Try another availability domain or region, or retry later. |
