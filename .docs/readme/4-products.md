## Product Categories Data Flow

- Added a `categories` table (migration `2025_10_13_145913_create_categories_table.php`) with `name`, `slug`, and `description` columns.
- Introduced `App\Models\Category` and an accompanying factory so tests and future seeders can generate catalogue records quickly.
- Seeded nine core departments via `CategorySeeder`, ranging from white goods to lighting; `DatabaseSeeder` now invokes it after user provisioning.
- Added a dedicated `products` table (migration `2025_10_13_151206_create_products_table.php`) covering catalogue metadata such as SKUs, sizing profiles, sustainability tags, and stock handling fields for future ordering workflows.
- `App\Models\Product` exposes JSON casts for size, care, and sustainability arrays, while `ProductFactory` produces realistic testing data.
- `ProductSeeder` attaches at least three sustainable-forward products to every category, capturing material notes, stock posture, and lead times so demo orders can reason about availability.
- Placeholder media assets are provisioned under `public/products/<slug>/image-{n}.png` and wired to each product so the list and detail screens always render imagery; when new items are added ensure multiple images exist to keep the detail pagination meaningful.
- `/products` resolves categories from the database, orders them by name, and hands them to the Inertia page so the grid renders live data.
- The React index view (`resources/js/pages/products.tsx`) displays the first image for each card, chips out key traits, and links through to per-product detail routes.
- The detail screen (`resources/js/pages/products/show.tsx`) paginates through every product image, surfaces stock posture, lead times, sustainability/care lists, and exposes selectable size options for future checkout flows.
- Feature coverage: `tests/Feature/ProductsPageTest.php` ensures the endpoint returns sorted categories for the UI.
