# Adding a domain module

Mirror the Tags module, in data-flow order:

1. Migration in `database/migrations`
2. Model and factory in `app/Domains/<Name>/Models`: `LogsModelActivity`, plus the `#[UseFactory]` / `#[UsePolicy]` attributes
3. Policy extending `Shared\Policies\AdminPolicy`
4. `Data/<Thing>Data.php`: rules, `prepareForPipeline()` normalization, `#[MapName(SnakeCaseMapper::class)]`
5. `Actions/{Create,Update,Delete}<Thing>Action.php`: transactions, explicit `columns()` mapping, `DomainRuleViolation` for blocked rules
6. `Queries/<Thing>Queries.php` for reads
7. `Resources/<Thing>Resource.php` (plus `Admin<Thing>Resource` if the admin needs more) with `fromModel()`
8. `Controllers/{Admin,Public}/<Thing>Controller.php` implementing `HasMiddleware` (`can:` per action), and a
   `routes/<module>.php` required from `routes/web.php`
9. `php artisan wayfinder:generate --with-form`
10. `resources/js/types/domains/<name>.ts` (mirroring the Resource in snake_case), the pages in
    `pages/admin/<things>/` (with their private form/dialog next to them) and any shared components in
    `components/custom/<name>/`
11. Sidebar entry in the right group of `navGroups` in `resources/js/components/custom/app-sidebar.tsx`
12. Tests in `tests/Feature/<Name>/{Actions,Queries,Admin,Public}/`
13. A feature doc in `docs/features/` (see CLAUDE.md)
14. Seed data, only if it is real, in `database/seeders`
