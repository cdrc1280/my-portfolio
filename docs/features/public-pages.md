# Public pages

Which modules feed each public page. Update this when a page gains or loses a section.

| Page | Route | Controller (in its module's `Controllers/Public`) | Content |
| --- | --- | --- | --- |
| Home | `/` | `HomeController` | 3 featured published [projects](projects.md), all [How I work](work-approach.md) items, the first 4 [services](services.md), skill groups from [tags](tags.md), the first 3 [roles](experience.md), up to 3 featured [testimonials](testimonials.md) |
| About | `/about` | `AboutController` | [Profile](profile.md) bio (Markdown), [experience, education, certificates](experience.md) |
| Projects | `/projects`, `/projects/{slug}` | `ProjectController` | [Projects](projects.md), tag filter, linked testimonial, next project |
| Services | `/services` | `ServicesController` | [Services](services.md) + engagement settings |
| Blog | `/blog`, `/blog/{slug}` | `BlogController` | [Blog](blog.md). 404 when disabled |
| Uses | `/uses` | `UsesController` | [Uses](uses.md) |
| Contact | `/contact` | `ContactController` | [Contact form](contact.md) |
| Résumé | `/resume` | `ResumeController` | PDF from [Site settings](profile.md) |
| Sitemap / robots | `/sitemap.xml`, `/robots.txt` | `SeoController` | [SEO](seo.md) |
| Share cards / feed | `/og/*.png`, `/blog/feed.xml` | `ShareCardController`, `FeedController` | [SEO](seo.md) |

## Layout: panels over the dot field

All body content sits on panels so text never lies directly on the dot background. The tokens are in `components/custom/section.tsx`:

- `surface`: a rounded, bordered, near-opaque panel in the page colour, with backdrop blur from `sm` up (none on phones). `bg-card` cards inside it still stand out.
- `panelPadding`: the standard panel padding.
- `Section` puts each section (label, title, description, content) on one panel. Home, About and Services use it.
- Other pages apply `surface` directly:
  - Projects: the tag filter
  - Blog: the post list, and the post body
  - Uses: one panel per category
  - Contact: the details aside
  - Case study: one panel per section
- Page intros (h1 + lead text) and the home hero stay directly on the dot field.

## Navigation

`layouts/public/navigation.ts` hides pages that have nothing to show. Blog follows the Settings switch. Services and Uses follow the shared `content` prop (`HandleInertiaRequests`: does each have at least one item). The routes stay reachable; they're just not linked.

## Theme

Dark by default. Light mode uses a soft off-white page (`#f6f4f1`) with slightly lighter cards instead of pure white. Emerald text keeps ≥4.5:1 contrast on it (tokens in `resources/css/app.css`; `theme-color` in `app.blade.php` and `use-appearance.tsx`).

All public pages also show the ["Ask about me" assistant](assistant.md) button while it's enabled.

Every page also gets the shared `site` prop (name, availability, social links, blog switch) from [Site settings](profile.md).

Code: the Home page is `app/Domains/Site/Controllers/Public/HomeController.php` (`routes/site.php`); each other page's controller lives in its module (see that module's doc).

Tests: `tests/Feature/*/Public/*`, plus `tests/Feature/Site/Public/PublicSiteSmokeTest.php`, which loads every public page with the real seeded content.
