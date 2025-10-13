import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/layouts/site-layout';

interface SetupRequiredProps {
    missingTables: string[];
}

export default function CheckoutSetupRequired({ missingTables }: SetupRequiredProps) {
    const formattedList = missingTables.join(', ');

    return (
        <SiteLayout>
            <Head title="Checkout Setup Required" />
            <section className="mx-auto max-w-2xl space-y-6 rounded-3xl border border-amber-200 bg-amber-50 p-10 shadow-sm dark:border-amber-600/40 dark:bg-amber-950/40">
                <h1 className="text-2xl font-semibold text-amber-800 dark:text-amber-300">
                    Checkout needs database setup
                </h1>
                <p className="text-sm text-amber-900/80 dark:text-amber-200/80">
                    The checkout feature is installed but its database tables have not been migrated yet.
                    Please run <code className="rounded bg-neutral-900 px-2 py-1 text-xs text-white">php artisan migrate</code>{' '}
                    and reload the page once the command finishes.
                </p>
                {missingTables.length > 0 && (
                    <div className="rounded-2xl border border-amber-300 bg-white/60 p-4 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-100">
                        <p className="font-semibold">Pending tables:</p>
                        <p>{formattedList}</p>
                    </div>
                )}
                <div className="flex flex-col gap-3 sm:flex-row">
                    <Link
                        href="/cart"
                        className="rounded-full border border-amber-400 px-6 py-3 text-sm font-semibold text-amber-800 transition hover:bg-amber-100 dark:border-amber-600 dark:text-amber-200 dark:hover:bg-amber-900"
                    >
                        Return to cart
                    </Link>
                    <a
                        href="https://laravel.com/docs/migrations"
                        target="_blank"
                        rel="noreferrer"
                        className="rounded-full border border-transparent bg-amber-500 px-6 py-3 text-sm font-semibold text-white transition hover:bg-amber-400 dark:bg-amber-400 dark:text-neutral-950 dark:hover:bg-amber-300"
                    >
                        Migration guide
                    </a>
                </div>
            </section>
        </SiteLayout>
    );
}

