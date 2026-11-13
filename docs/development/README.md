# Development

## Everyday commands

```bash
make help       # list every target
make test       # Pest (parallel)
make lint       # Pint, Larastan (level 7), ESLint, Prettier, tsc
make fix        # auto-fix style
make ci         # lint + test, the same gates CI runs
make fresh      # migrate:fresh --seed
make shell      # shell in the app container
make logs       # tail all services
make hooks      # enable the git pre-push hook (runs the test suite before every push)
```

Everything runs in Docker. Never assume PHP, Composer or Node exist on the host.

## Git hooks

`.githooks/pre-push` runs the Pest suite in the app container and blocks the push if anything fails. It is opt-in:
run `make hooks` (or `scripts/setup-git-hooks.sh`) once per clone. It sets `core.hooksPath` to `.githooks`. Skip it for
a single push with `git push --no-verify`.

## Services (dev stack)

The dev stack (`docker-compose.yml`) runs `app` (PHP-FPM), `nginx`, `queue` (worker), `scheduler`, `node` (Vite), `db`
(MySQL 8.4), `redis` and `mailpit`.

The scheduler runs `github:refresh` hourly, and contact-form emails are sent through the queue.


## Gotchas

- **Cached objects:** Laravel 13 only unserializes allowlisted classes. Add any class you cache to
  `config/cache.php` → `serializable_classes`. The test cache store serializes values, so it catches a missing entry.
- **No `env_file` on the dev `app` service:** Docker env vars override `phpunit.xml`, which would point tests at MySQL.
- **nginx config:** the config **directory** is mounted, not a single file. A single-file bind mount keeps serving stale config after edits.
- **Markdown** is rendered server-side with CommonMark and raw HTML is stripped.
