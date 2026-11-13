# Portfolio

A personal portfolio with a built-in admin CMS, built on Laravel 13, Inertia 3, React 19, TypeScript and Tailwind 4.
The backend is organized by business domain (`app/Domains/<Name>`).

Public site: home, projects and case studies, services, about, contact (budget in your own currency), an optional
blog and `/uses` page, and a free "Ask about me" assistant. Admin (`/admin`): every page's content, the contact
inbox and a dashboard.

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

## Documentation

| Where | What |
|---|---|
| [docs/architecture/overview.md](docs/architecture/overview.md) | Backend and frontend layout, request flow, tests |
| [docs/architecture/adding-a-module.md](docs/architecture/adding-a-module.md) | Checklist for a new module (mirror Tags) |
| [docs/architecture/design-system.md](docs/architecture/design-system.md) | Theme, components, forms and error fallbacks |
| [docs/development/README.md](docs/development/README.md) | Commands, git hooks, dev services, gotchas |
| [docs/operations/README.md](docs/operations/README.md) | Production build, environment variables, release notes |
| [docs/operations/vercel.md](docs/operations/vercel.md) | Step-by-step deploy to Vercel (free), the main host |
| [docs/operations/oracle-cloud.md](docs/operations/oracle-cloud.md) | Alternative: self-host on a free Oracle Cloud VM |
| [docs/features/README.md](docs/features/README.md) | One doc per module/page: routes, fields, behavior, code, tests |

Project rules for contributors and AI agents are in [CLAUDE.md](CLAUDE.md).
