# Repository Guidelines

## Project Structure & Module Organization
Core Laravel API code lives in `app/`, with domain logic grouped by feature; HTTP routes are defined in `routes/web.php` and `routes/api.php`. Inertia-powered React UI assets sit under `resources/js`, with Blade fallbacks in `resources/views` and Tailwind styles in `resources/css`. Database migrations, factories, and seeders are under `database/`. Compiled assets target `public/`. Automated tests stay in `tests/`, mirroring the application layers.

## Build, Test, and Development Commands
After cloning, copy `.env.example` to `.env`, add service credentials, and run `php artisan key:generate`. Install dependencies via `composer install` and `npm install`. Boot the full stack with `composer dev`, which runs `php artisan serve`, the queue listener, and Vite in sync. Use `npm run dev` when you only need the Vite dev server, and `npm run build` (or `npm run build:ssr`) for production bundles. Run one-off tasks with Artisan (e.g., `php artisan migrate --seed`) and clear caches through `php artisan config:clear`.

## Coding Style & Naming Conventions
Adhere to PSR-12 for PHP; controllers, actions, and jobs use StudlyCase class names and descriptive method names. React components and hooks are PascalCase files inside `resources/js`, while utilities stay camelCase. The repo enforces 4-space indentation and LF endings via `.editorconfig`. Run `npm run lint` to apply the shared flat ESLint ruleset, and `npm run format` to apply Prettier with Tailwind ordering. Format PHP with `vendor/bin/pint`. Keep route names kebab-case and environment keys UPPER_SNAKE_CASE.

## Testing Guidelines
Tests use Pest; place new feature stories in `tests/Feature` and unit specs in `tests/Unit`, naming each file `*Test.php`. Run the suite with `composer test` or `php artisan test`. Cover new endpoints, validation rules, and React components that manage data mutations. Snapshot outputs belong in `tests/__snapshots__`, matching the test filename.

## Commit & Pull Request Guidelines
Git history favors short, imperative subjects (`init`, `Initial commit`). Continue with <50-character commands plus optional body context. Reference issue IDs where available. Pull requests should include: concise summary, setup notes for reviewers (env variables, migrations), and before/after screenshots or terminal output for user-facing changes. Confirm lint and test commands have passed before requesting review.
