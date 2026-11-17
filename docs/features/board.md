# Board

The admin's private kanban board for tasks, ideas and notes. Nothing on it is shown on the public site.

- **Admin:** `/admin/board` (sidebar: Workspace group, under Dashboard)
- **Public:** none

## Fields

| Field | Rules |
| --- | --- |
| `title` | required, ≤160, trimmed |
| `notes` | nullable, ≤5000 |
| `status` | `ideas` \| `todo` \| `doing` \| `done` (the columns, left to right); defaults to `todo` |
| `priority` | `low` \| `normal` \| `high`; defaults to `normal` |
| `due_on` | nullable date |
| `position` | manual order within a column (`HasPosition`) |

## Behavior

- **Columns** are fixed (`CardStatus`), not records. `BoardQueries::columns()` returns all four in order, each with its cards by position.
- **Add** from a column's `+` button: the new card starts in that column, at the bottom.
- **Edit** in a dialog. Its Column select moves a card without dragging. A status change puts the card at the bottom of the new column (`UpdateBoardCardAction`).
- **Drag and drop** (pointer or keyboard, by the grip handle) within or between columns. The drop is shown at once, then `POST /admin/board/cards/{card}/move` sends `status` plus the target column's ids in the new order (`MoveCardData`). `MoveBoardCardAction` sets the status and renumbers that column. If the moved card is missing from `ids`, or an id belongs to another column, it throws `DomainRuleViolation` (error toast) and rolls back.
- **Clear** in the Done column's header deletes every done card after a confirm (`ClearDoneCardsAction`, which deletes model by model so each card is audit-logged).
- **Due dates:** `BoardCardResource` flags `overdue` (past due, not done) and `due_today`. The page header counts open (not done) cards and overdue ones.
- Cards are audit-logged like every other model (`LogsModelActivity`).

## Admin page

No side rail. The board takes the full width: 1 column on phones, 2 on tablets, 4 on wide screens.

## Code

- Controller: `app/Domains/Board/Controllers/Admin/BoardCardController.php` · routes: `routes/board.php`
- Domain: `app/Domains/Board`: `Data/{BoardCard,MoveCard}Data`, `Actions/{Create,Update,Delete,Move}BoardCardAction` and `ClearDoneCardsAction`, `Queries/BoardQueries`, `Resources/BoardCardResource`, `Enums/{CardStatus,CardPriority}`, `Models/BoardCard`, `Policies/BoardCardPolicy`
- Migration: `2026_11_17_000100_create_board_cards_table` · factory: `BoardCardFactory`
- Frontend: `pages/admin/board/*` (page, `kanban-board.tsx`, `card-dialog.tsx`), `types/domains/board.ts`

## Tests

`tests/Feature/Board/Actions/*`, `tests/Feature/Board/Admin/*`
