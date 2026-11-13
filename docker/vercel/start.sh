#!/bin/sh
# Container entrypoint on Vercel. Runs once per instance start.
#
# Vercel kills an instance whose server isn't accepting connections on $PORT within about
# 28 seconds, so the web server must start straight away: nothing slow may run before it.
set -u
cd /app

# 1. SSR server for search engines and link previews. If it stops, the site falls back to
#    client-side rendering, so it never takes the site down.
node bootstrap/ssr/app.js >&2 &

# 2. Migrate (under a database lock, safe with many instances) and seed on the very first release,
#    in the background. Over a remote database this can take longer than the startup window; on an
#    up-to-date database it's a few quick queries. A database problem is logged, not fatal.
#    It uses Neon's direct (unpooled) connection when there is one: the migration lock is a
#    session lock, which a transaction pooler can't hold reliably.
(
    if [ -n "${DATABASE_URL_UNPOOLED:-}" ]; then
        export DB_URL="$DATABASE_URL_UNPOOLED"
    fi
    php artisan app:release --no-interaction >&2 || echo "app:release failed - check the database environment variables" >&2
) &

# 3. Serve.
exec frankenphp run --config /etc/frankenphp/Caddyfile
