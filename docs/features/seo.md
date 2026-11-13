# SEO

- `GET /sitemap.xml`: `SitemapQuery`. Lists home (1.0), projects (0.9), about (0.8), contact (0.7), every published project (0.8) and, while the blog is enabled, the blog index and published posts (0.6). Services (0.8) and Uses (0.4) appear only when they have content. `updated_at` is the lastmod.
- `GET /robots.txt`: in production, disallows `/admin` and `/settings` and links the sitemap. In any other environment, disallows everything.
- **Meta tags:** `components/custom/seo.tsx` (`Seo`) renders title, description, canonical, Open Graph and Twitter tags, plus an RSS `<link rel="alternate">` while the blog is on. The image is the page's own (`image`, e.g. a project cover), then its generated card (`card`), then the site-wide `og_image` from Settings.
- **Share cards** (`ShareCardController`, `RenderShareCardAction`, `ShareCard`): 1200×630 PNGs in the dark theme, using Geist fonts from `resources/fonts/og` (OFL). Each card is rendered once and stored on the local disk under `og/`, keyed by a content hash. Bump `RenderShareCardAction::VERSION` after a design change. Only fixed pages get one (`/og/{home|about|projects|services|contact|blog|uses}.png`), plus published projects (`/og/projects/{slug}.png`) and, while the blog is on, published posts (`/og/blog/{slug}.png`). There is no free-text input.
- **Structured data:** `JsonLd` and `personSchema` in `seo.tsx`. Home has `WebSite` + `Person`, About a `ProfilePage`, case studies a `CreativeWork`, posts a `BlogPosting`. They are built only from Settings and the content's own fields.
- **RSS:** `GET /blog/feed.xml` (`FeedController`, view `seo/feed.blade.php`) serves the 20 newest published posts. It returns 404 while the blog is off.

## Code

`app/Domains/Seo/Controllers/Public/SeoController.php`, `app/Domains/Seo/Controllers/Public/ShareCardController.php` (routes: `routes/seo.php`; the RSS feed is `Blog/Controllers/Public/FeedController.php` in `routes/blog.php`), `app/Domains/Seo` (`SitemapQuery`, `RenderShareCardAction`, `SitemapEntry`, `ShareCard`), `resources/views/seo/*`

## Tests

`tests/Feature/Seo/Public/*`, `tests/Feature/Seo/Queries/*`, `tests/Unit/Seo/*`
