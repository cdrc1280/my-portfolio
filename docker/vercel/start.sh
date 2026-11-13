#!/bin/sh
# Container entrypoint on Vercel. Runs once per instance start.
set -u
cd /app

# 1. SSR server for search engines and link previews. If it stops, the site falls back to
#    client-side rendering, so it never takes the site down.
node bootstrap/ssr/app.js >&2 &

# 2. Migrate (under a database lock, safe with many instances) and seed on the very first release.
#    A database problem is logged, not fatal: the site still starts and shows its error pages.
php artisan app:release --no-interaction >&2 || echo "app:release failed - check the database environment variables" >&2

# 3. Serve.
exec frankenphp run --config /etc/frankenphp/Caddyfile
