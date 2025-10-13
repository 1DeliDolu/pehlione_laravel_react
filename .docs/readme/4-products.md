## Product Categories Data Flow

- Added a `categories` table (migration `2025_10_13_145913_create_categories_table.php`) with `name`, `slug`, and `description` columns.
- Introduced `App\Models\Category` and an accompanying factory so tests and future seeders can generate catalogue records quickly.
- Seeded nine core departments via `CategorySeeder`, ranging from white goods to lighting; `DatabaseSeeder` now invokes it after user provisioning.
- `/products` resolves categories from the database, orders them by name, and hands them to the Inertia page so the grid renders live data.
- The React view (`resources/js/pages/products.tsx`) expects `categories` from the server and falls back to the seeded list if the database is empty.
- Feature coverage: `tests/Feature/ProductsPageTest.php` ensures the endpoint returns sorted categories for the UI.
