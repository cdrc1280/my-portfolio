# Account settings

Login account management (separate from [Site settings](profile.md)).

- **Routes:** `/settings` → `/settings/profile` (name, email, delete account), `/settings/security` (password, two-factor), `/settings/appearance` (theme)
- **Layout:** `resources/js/layouts/settings/layout.tsx`. Nav on the left, the form (max-w-2xl), and from `xl` up an Account / Security side rail (name, email, verified, member since, two-factor status, tips)

## Code

- Controllers: `app/Domains/Account/Controllers/Settings/ProfileController.php`, `app/Domains/Account/Controllers/Settings/SecurityController.php` · routes: `routes/account.php`
- Domain: `app/Domains/Account`: `Data/{AccountProfile,PasswordUpdate,DeleteAccount}Data`, `Resources/UserResource` (shared as `auth.user`). Fortify's `TwoFactorAuthenticationRequest` stays: it manages two-factor state and validates nothing.
- Pages: `resources/js/pages/settings/*`
- Two-factor: `components/manage-two-factor.tsx`, `components/two-factor-setup-modal.tsx`

## Tests

`tests/Feature/Account/Auth/*`, `tests/Feature/Account/Settings/*`
