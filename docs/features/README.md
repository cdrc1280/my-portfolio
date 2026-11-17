# Module docs

One file per CMS module or page. Each one says what the module is for, where it shows up, its fields and rules, and where its code and tests live.

**Keep these in sync.** When you change a module's fields, validation, routes, behavior, admin page or public page, update its doc in the same change. See `CLAUDE.md` at the repo root.

| Module | Admin | Public | Doc |
| --- | --- | --- | --- |
| Dashboard | `/admin` | — | [dashboard.md](dashboard.md) |
| Board | `/admin/board` | — (private) | [board.md](board.md) |
| Projects | `/admin/projects` | `/projects`, `/projects/{slug}`, home | [projects.md](projects.md) |
| Blog | `/admin/posts` | `/blog`, `/blog/{slug}` | [blog.md](blog.md) |
| Experience | `/admin/experience` | `/about`, home | [experience.md](experience.md) |
| Services | `/admin/services` | `/services`, home | [services.md](services.md) |
| Testimonials | `/admin/testimonials` | home, project pages | [testimonials.md](testimonials.md) |
| Tags & skills | `/admin/tags` | project/blog filters, home skills | [tags.md](tags.md) |
| How I work | `/admin/work-approaches` | home | [work-approach.md](work-approach.md) |
| Uses | `/admin/uses` | `/uses` | [uses.md](uses.md) |
| "Ask about me" assistant | Settings → Features | chat widget on every public page, `POST /assistant` | [assistant.md](assistant.md) |
| Inbox / contact | `/admin/messages` | `/contact` | [contact.md](contact.md) |
| Site settings (profile) | `/admin/profile` | every page | [profile.md](profile.md) |
| Account settings | `/settings/*` | — | [account-settings.md](account-settings.md) |
| SEO | — | `/sitemap.xml`, `/robots.txt`, `/og/*.png`, `/blog/feed.xml`, JSON-LD | [seo.md](seo.md) |
| Integrations (GitHub, Calendly, analytics) | dashboard tiles | analytics script | [integrations.md](integrations.md) |
| Public pages map | — | all | [public-pages.md](public-pages.md) |

## Seed data

`PortfolioSeeder` holds **real content only**: the résumé (profile, skills, roles, education, certificates, and four services restated from résumé lines), plus the project URLs, stacks and screenshots the owner confirmed, plus the "Developer Portfolio & CMS" case study, which describes this repository. If `storage/app/seed/resume.pdf` exists (gitignored), it is attached as the downloadable résumé. Never seed invented content (testimonials, metrics, services, posts, messages); `PublicSiteSmokeTest` checks this.

## Shared admin conventions

- **Full-width layout.** Admin pages use the whole content area. List and settings pages use `AdminSplit` (`resources/js/components/custom/admin-split.tsx`): the main content on the left, and a sticky 20rem side rail on the right from `xl` up, with a summary, breakdowns and tips. Below `xl` the rail stacks underneath. Project and post forms have their own built-in side column.
- **CRUD lists** use `SortableCrudList`: add/edit dialogs, delete with confirmation, and drag to reorder when a reorder URL is given.
- **Add/edit forms** use `FormDialog`: a centred dialog from 768px up, and a bottom drawer on phones (fields scroll, Save and Cancel stay pinned at the bottom). Delete confirmations stay alert dialogs.
- **Sidebar:** the collapse toggle sits in the sidebar header, right of the name. When collapsed, the logo is hidden and the toggle stays at the top of the icon column. On mobile it moves to the top bar (`app-sidebar.tsx`, `app-sidebar-header.tsx`), and the off-canvas sidebar closes as soon as a link starts a visit. Links are grouped (`NavGroup`, rendered by `nav-main.tsx`): **Workspace** (Dashboard, Board, Inbox), **Portfolio** (Projects, Experience, Testimonials, Tags & skills), **Pages** (Services, How I work, Blog, Uses), **Site** (Settings).
- **Top bar:** notification bell, theme toggle (`ThemeToggle`, the same one as the public header), account menu.
- **Loading** uses `AdminPageSkeleton`, which picks the list/form/detail/dashboard shape from the URL.
- **Authorization:** every admin route sits behind `auth` + `can:access-admin`, and each controller also authorizes through its domain policy (`AdminPolicy`).
