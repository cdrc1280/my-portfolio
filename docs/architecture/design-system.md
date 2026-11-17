# Design system ("Engineer's case study")

Calm, technical and minimal: shadcn zinc with one emerald accent, Geist and Geist Mono, dark by default
with a persisted light/dark toggle. Admin and login use the same tokens with no decoration.

- **Tokens** live in `resources/css/app.css`. `--primary` is the single accent, used for CTAs, links, focus
  rings and active states. Body copy uses `--muted-foreground` and headings use `--foreground`. Components
  never hardcode colours.
- **Shared components** (`resources/js/components/custom/`): `SectionLabel` (`// projects`), `Section`,
  `PageIntro`, `TechBadge`, `ProjectCard`, `TimelineItem`, `FolderTree`, `ThemeToggle`, `SiteHeader`,
  `SiteFooter`, `Reveal` / `RevealWords`.
- **Case studies:** project pages render Problem → My role → Architecture → Key decisions → Challenges → Results →
  Screenshots → Client feedback from the CMS fields; empty sections are skipped. Each section is a `surface` panel.
- **Panels:** public content sits on `surface` panels (`components/custom/section.tsx`) so text never lies on the dot
  field. Light mode is a soft off-white (`#f6f4f1`), not pure white.
- **Motion:** an interactive dot-field backdrop (WebGL, idle when the pointer is still), headline words
  rising into place, and fade-ups as sections enter. The reveals are plain CSS (`[data-reveal]`,
  `.reveal-word`, `.draw-in` in `app.css`, triggered by an IntersectionObserver in `reveal.tsx`), so the
  public pages ship no animation library. With `prefers-reduced-motion`, the dots are static and nothing
  animates.
- **Bundle:** the admin sidebar shell is lazy-loaded inside `layouts/admin-layout.tsx`, so public visitors
  never download admin code.
- Headlines take `*asterisks*` around words to set them in the accent colour.


## Forms and errors

- **Fields** use `FormField` (`components/custom/form-field.tsx`): label, required marker, the input, a hint and the
  inline error. Required fields pass `required`, which shows the marker and sets `aria-required`.
- **Never the browser's own validation:** every `<form>` sets `noValidate`. Errors come from the server's Data rules and
  appear inline under each field.
- **Fallbacks:** a form submit that fails for a non-validation reason (rate limit, expired session, upload too large,
  server error) returns to the page with an error toast (`App\Shared\Exceptions\FriendlyErrorResponse`). A page load
  shows the themed error page. A React rendering crash shows a recovery screen (`AppErrorBoundary`), and a network
  failure shows a toast (`useHttpErrorFallback`).
- **Toasts:** Sonner, top right below the sticky header, 5 seconds, with a close button. Each is a card with a coloured
  left edge and icon per type (emerald success, red error, amber warning, sky info), styled in `app.tsx`. A repeated
  flash toast (same type and message) replaces the one on screen instead of stacking.
