import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/layouts/site-layout';

export default function Home() {
    return (
        <SiteLayout>
            <Head title="Home" />
            <section className="grid gap-10 lg:grid-cols-2 lg:items-center">
                <div className="space-y-6">
                    <p className="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-700 dark:bg-amber-900/40 dark:text-amber-200">
                        Welcome to PehliONE
                    </p>
                    <h1 className="text-4xl font-semibold tracking-tight sm:text-5xl">
                        Your one-stop marketplace for curated lifestyle essentials.
                    </h1>
                    <p className="max-w-xl text-base text-neutral-600 dark:text-neutral-300">
                        Discover thoughtfully sourced collections across fashion, tech, and home. PehliONE unifies secure shopping, personalised recommendations, and responsive support so customers and partners stay connected.
                    </p>
                    <div className="flex flex-wrap gap-3">
                        <Link
                            href="/products"
                            prefetch
                            className="rounded-full bg-neutral-900 px-6 py-2 text-sm font-semibold text-white transition hover:bg-neutral-800 dark:bg-neutral-100 dark:text-neutral-900 dark:hover:bg-neutral-200"
                        >
                            Explore Products
                        </Link>
                        <Link
                            href="/connection"
                            prefetch
                            className="rounded-full border border-neutral-300 px-6 py-2 text-sm font-semibold text-neutral-800 transition hover:border-neutral-400 hover:text-neutral-900 dark:border-neutral-700 dark:text-neutral-100 dark:hover:border-neutral-500"
                        >
                            Contact Team
                        </Link>
                    </div>
                </div>
                <div className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-100 via-white to-amber-200 p-8 shadow-lg dark:from-neutral-800 dark:via-neutral-900 dark:to-neutral-800">
                    <div className="space-y-4 text-neutral-700 dark:text-neutral-200">
                        <h2 className="text-2xl font-semibold">Seamless Journeys</h2>
                        <p>
                            Inertia-powered React pages keep browsing fluid while Laravel powers authentication, inventory, and fulfilment workflows.
                        </p>
                        <div className="grid gap-4 text-sm">
                            <div className="rounded-2xl bg-white/80 p-4 shadow-sm backdrop-blur dark:bg-neutral-800/70">
                                <p className="font-semibold text-neutral-900 dark:text-neutral-100">Tailored Dashboards</p>
                                <p className="mt-1 text-neutral-600 dark:text-neutral-300">
                                    Role-aware layouts help admins, employees, and customers collaborate and act quickly.
                                </p>
                            </div>
                            <div className="rounded-2xl bg-white/80 p-4 shadow-sm backdrop-blur dark:bg-neutral-800/70">
                                <p className="font-semibold text-neutral-900 dark:text-neutral-100">Real-time Updates</p>
                                <p className="mt-1 text-neutral-600 dark:text-neutral-300">
                                    Integrated queue listeners and notifications keep inventory and conversations in sync.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </SiteLayout>
    );
}
