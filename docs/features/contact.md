# Contact & inbox

The public project-brief form and the admin inbox.

- **Public:** `GET /contact`, `POST /contact` (`throttle:contact`)
- **Admin:** `/admin/messages` (`?filter=unread`, paginated), `/admin/messages/{id}`, `PATCH /admin/messages/{id}/unread`, `DELETE /admin/messages/{id}`

## Form fields

| Field | Rules |
| --- | --- |
| `name` | required, ≤100 |
| `email` | required, RFC email, ≤150 |
| `project_type` | `new_build` \| `feature_work` \| `maintenance` \| `legacy_upgrade` \| `consulting` \| `other` |
| `project_type_other` | the sender's own words, max 150; required when `project_type` is `other`, otherwise dropped |
| `budget_currency` | `PHP` \| `USD` \| `EUR` \| `GBP` \| `AUD` \| `CAD` \| `SGD` (required) |
| `budget` | bracket: `under_1k` \| `1k_3k` \| `3k_8k` \| `8k_plus` \| `not_sure` \| `other` (names kept from the USD-only days) |
| `budget_other` | the sender's own amount, max 150; required when `budget` is `other`, otherwise dropped |
| `timeline` | `asap` \| `within_month` \| `one_to_three_months` \| `flexible` \| `other` |
| `timeline_other` | the sender's own words, max 150; required when `timeline` is `other`, otherwise dropped |
| `message` | required, 20–5000 |
| honeypot | must be empty |

## Behavior

- **Budgets in the client's currency:** a bracket is stored with its currency. `Currency::thresholds()` gives each currency rounded, roughly equivalent brackets: ₱50k / ₱150k / ₱450k, $1k / $3k / $8k, and so on. `BudgetRange::labelIn()` labels it, and `ContactMessage::budgetLabel()` is what the inbox and owner email show. The form preselects PHP for visitors on Philippine time and USD otherwise. Briefs from before currencies were added count as USD.
- **"Other" answers:** project type, budget and timeline each have an `other` option ("Something else" / "Another amount"). The controller passes that value as `otherValue`; picking it shows a text box under that dropdown, and the text is saved in the matching `*_other` column. `ContactMessage::projectTypeLabel()`, `budgetLabel()` and `timelineLabel()` return the sender's words in that case, and the inbox and owner email use them. Currency has no "other": it sets the bracket amounts.
- **Dashboard mix:** budget brackets are compared across currencies and labelled in USD ("USD equivalent").
- **Analytics:** a successful send fires the `Contact brief sent` event (see [integrations.md](integrations.md)).

- **Required fields** (name, email, project type, timeline, currency, budget, message) are marked with an asterisk. Errors show inline under each field, never as browser bubbles.
- **Rate limit:** 3 briefs per hour per IP (`portfolio.contact.per_hour`). Going over returns to the form with an error toast saying when to try again, and keeps everything the visitor typed (`succeeded()` guard, `preserveState`).
- **Spam:** a filled honeypot is logged and silently dropped. The bot gets the same success response as a person.
- **Privacy:** the IP is stored only as an HMAC (`ip_hash`, keyed by `APP_KEY`).
- **On submit,** `ContactMessageSubmitted` fires two queued listeners: `SendContactMessageNotification` (to `portfolio.contact.notify_email`) and `SendContactAutoReply` (confirmation to the sender, including the response time).
- **Read state:** opening a message marks it read. "Mark unread" reverses that.
- **Message page:** the body is on the left; sender and project brief are in the side rail.

## Admin side rail (inbox)

Unread and total counts, a project-type breakdown for the current page, how many gave a budget, and tips.

## Code

- Controllers: `app/Domains/Contact/Controllers/Admin/MessageController.php`, `app/Domains/Contact/Controllers/Public/ContactController.php` · routes: `routes/contact.php`
- Domain: `app/Domains/Contact`: `Data/ContactMessageData` (honeypot via `isSpam()`), `Actions/SubmitContactMessageAction` (hashes the IP passed by the controller), mark read/unread and delete actions, `Queries/ContactMessageQueries`, `Resources/ContactMessageResource`, events, listeners, notifications, enums (`BudgetRange`, `Currency`, …), `ContactMessageQueryBuilder`
- Frontend: `components/custom/contact/*`, `pages/admin/messages/*` (page + its private components), `types/domains/contact.ts`

## Tests

`tests/Feature/Contact/Actions/*`, `tests/Feature/Contact/Admin/*`, `tests/Feature/Contact/Public/*`, `tests/Unit/Contact/*`
