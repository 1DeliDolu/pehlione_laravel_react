## Cart Workflow Overview

- Carts are stored in `carts` with related rows in `cart_items`. Each user owns a single `active` cart; future order submission will timestamp `submitted_at` and allow a new cart to spawn.
- `CartController` handles listing (`/cart`), adding items, adjusting quantities, and removing rows. All routes require authenticated, verified users.
- `resources/js/pages/products/show.tsx` powers the add-to-cart flow with size selection, quantity controls, and a POST to `cart.items.store`.
- The cart summary screen (`resources/js/pages/cart/index.tsx`) surfaces item details, totals, and lightweight update/remove actions while checkout is under construction.
- A shared Inertia prop exposes the cart item count so the navbar can display a live badge for signed-in users (`HandleInertiaRequests`).
- Tests in `tests/Feature/CartTest.php` cover add, increment, and authorization rules.
