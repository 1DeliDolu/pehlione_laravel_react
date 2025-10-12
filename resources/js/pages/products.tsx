import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/layouts/site-layout';

const sampleCategories = [
    { name: 'New Arrivals', description: 'Fresh seasonal picks curated by our merchandising team.' },
    { name: 'Tech & Accessories', description: 'Latest devices, wearables, and smart-living essentials.' },
    { name: 'Home & Living', description: 'Organisers, decor, and everyday comforts for modern homes.' },
];

export default function Products() {
    return (
        <SiteLayout>
            <Head title="Products" />
            <section className="space-y-8">
                <div className="space-y-4">
                    <h1 className="text-3xl font-semibold tracking-tight">Product Collections</h1>
                    <p className="max-w-3xl text-neutral-600 dark:text-neutral-300">
                        Browse curated product groups and prepare the UI for pagination, filtering, and cart interactions. The grid below is a placeholder until the Product model and API endpoints are ready.
                    </p>
                </div>
                <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {sampleCategories.map((category) => (
                        <div
                            key={category.name}
                            className="flex h-full flex-col justify-between rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition hover:border-amber-500 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-amber-400"
                        >
                            <div className="space-y-3">
                                <h2 className="text-xl font-semibold text-neutral-900 dark:text-neutral-100">
                                    {category.name}
                                </h2>
                                <p className="text-sm text-neutral-600 dark:text-neutral-300">
                                    {category.description}
                                </p>
                            </div>
                            <Link
                                href="#"
                                className="mt-4 inline-flex items-center text-sm font-semibold text-amber-600 transition hover:text-amber-500 dark:text-amber-400 dark:hover:text-amber-300"
                            >
                                View upcoming lineup ?
                            </Link>
                        </div>
                    ))}
                </div>
            </section>
        </SiteLayout>
    );
}
