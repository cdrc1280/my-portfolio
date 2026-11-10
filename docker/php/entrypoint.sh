#!/bin/sh
set -e

# Warm framework caches on container start, when the real env is known.
if [ "${APP_ENV}" = "production" ] && [ "$1" = "php-fpm" ]; then
    php artisan storage:link --force >/dev/null 2>&1 || true
    php artisan optimize
    if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
        php artisan migrate --force
    fi
fi

exec "$@"
