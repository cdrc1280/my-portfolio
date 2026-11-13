#!/usr/bin/env bash
# Builds and (re)starts the production stack on the server. Safe to run for every update.
#   bash scripts/deploy.sh           # deploy the current branch (git pull first)
#   bash scripts/deploy.sh --seed    # first deploy only: also create the admin and seed real content
set -euo pipefail

cd "$(dirname "$0")/.."
COMPOSE="docker compose -f docker-compose.prod.yml"

if [ ! -f .env ]; then
    echo "Missing .env. Copy .env.production.example to .env and fill it in first." >&2
    exit 1
fi

if grep -q "CHANGE" .env; then
    echo "Your .env still has CHANGE placeholders. Fill them in first:" >&2
    grep -n "CHANGE" .env >&2
    exit 1
fi

echo "==> Pulling the latest code"
git pull --ff-only

echo "==> Building images (the first build takes several minutes)"
$COMPOSE build

if ! grep -q "^APP_KEY=base64:" .env; then
    echo "==> Generating APP_KEY"
    key=$($COMPOSE run --rm --no-deps -T app php artisan key:generate --show --no-ansi)
    sed -i "s|^APP_KEY=.*|APP_KEY=${key}|" .env
fi

echo "==> Starting the stack (waits for MySQL to be healthy)"
$COMPOSE up -d --remove-orphans --wait

echo "==> Running migrations (the only place they run, so they never race)"
$COMPOSE exec -T app php artisan migrate --force

if [ "${1:-}" = "--seed" ]; then
    echo "==> Seeding the admin account and portfolio content"
    $COMPOSE exec -T app php artisan db:seed --force
fi

echo "==> Clearing stale caches and restarting workers"
$COMPOSE exec -T app php artisan optimize
$COMPOSE exec -T app php artisan queue:restart

echo "==> Removing old images"
docker image prune -f >/dev/null

echo
$COMPOSE ps
echo
echo "Deployed. Visit $(grep '^APP_URL=' .env | cut -d= -f2-)"
