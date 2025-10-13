## Employee Seeder Updates

- Extended `database/seeders/DatabaseSeeder.php` to seed predefined employee users via `User::updateOrCreate` so roles/passwords stay in sync when reseeding.
- Accounts seeded (all using password `D0cker` and an immediate `email_verified_at` timestamp unless noted):
  - Test User — `test@example.com` (role `employee`, password `password`)
  - Kunden — `kunden@pehlione.com` (role `kunden`)
  - Marketing — `marketing@pehlione.com` (role `marketing`)
  - Lager — `lager@pehlione.com` (role `lager`)
  - Vertrieb — `vertrieb@pehlione.com` (role `vertrieb`)
  - Admin — `admin@pehlione.com` (role `admin`)
- Run `php artisan db:seed` to insert/update the users, or `php artisan migrate:fresh --seed` for a clean reset.

## Role-Based Access

- Added `App\Enums\Role` and a `role` column migration so staff accounts carry a first-class enum-backed role value.
- `app/Models/User.php` now casts `role` to the enum; the `UserFactory` defaults to the `employee` role for generated data.
- Documentation routes are behind `auth`/`verified`; `DocumentationController` filters sections based on `config/docs.php` so (for example) a Vertrieb user only sees the Vertrieb section while Admin can see every department.
- Update the role restrictions in `config/docs.php` when new department folders are added under `.docs/`.
