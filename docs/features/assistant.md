# "Ask about me" assistant

A free chat widget on every public page. It answers visitors' questions only from this site's content. There is **no AI service**: no API key, no cost, and it cannot invent anything.

- **Public:** a floating "Ask about {first name}" button with the site's strata logo mark, so it reads as a site feature rather than a live chat (`components/custom/assistant/assistant-widget.tsx`, mounted in `public-layout.tsx`). The panel header shows the owner's avatar (the logo mark when none is set) and says "Automated · answers come only from this site". The widget calls `POST /assistant` (`assistant.ask`, `throttle:assistant` = 20 per minute per IP).
- **Admin:** Settings → Features → "Ask about me" assistant (`profiles.assistant_enabled`, on by default). When it's off, the button is hidden and the endpoint returns 404.

## How it answers

`AnswerQuestionQuery` normalizes the question, then picks, in this order:

1. A **project** named by its bracketed acronym (`DTS`), all or two of its title words, or one long word only that project's title uses ("profiling").
2. A **role** named by company. Confidential roles are never matched by company name and are shown as "a confidential client".
3. **Skills**: tags named in the question, including short forms (`vue` → Vue.js, `html` → HTML5, `ts` → TypeScript). For each, the answer lists the roles and published projects that use it.
4. An **unknown skill** ("Do you know Rust?"): says it isn't listed rather than claiming it.
5. A **topic**, by keyword score: rates, availability, contact, résumé, education, certificates, experience, projects, services, skills, location, about, greeting, thanks. Patterns are whole words; a trailing `*` matches word endings.
6. Otherwise, an honest **fallback** ("I don't have an answer for that") with suggestions and the contact link.

Rates come from `starting_rate` when it's set. Otherwise the reply says rates depend on scope and points to the brief form. It never states a number that isn't in Settings.

Replies (`AssistantReply`) are plain text: paragraphs, bullets, links and suggested follow-ups. They are never HTML. The widget renders internal links with Inertia `<Link>`, and sends Laravel's `XSRF-TOKEN` cookie as the `X-XSRF-TOKEN` header. Each question fires the `Assistant question` analytics event.

## Extending

To cover a new kind of question, add a topic pattern and a reply method. A language model could be added later behind the same `AssistantReply` shape.

## Code

`app/Domains/Assistant` (`AnswerQuestionQuery`, `AssistantReply`), `app/Domains/Assistant/Controllers/Public/AssistantController.php` (`routes/assistant.php`), rate limiter in `AppServiceProvider`, `resources/js/components/custom/assistant/*`, `resources/js/types/domains/assistant.ts`

## Tests

`tests/Feature/Assistant/Public/*`
