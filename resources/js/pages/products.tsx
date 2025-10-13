import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/layouts/site-layout';

interface CategorySummary {
    id?: number;
    name: string;
    slug?: string;
    description?: string | null;
}

interface ProductsPageProps {
    categories?: CategorySummary[];
}

const fallbackCategories: CategorySummary[] = [
    {
        name: 'White Goods',
        slug: 'white-goods',
        description:
            'Large household appliances like refrigerators, washers, and dryers designed for everyday reliability.',
    },
    {
        name: 'Consumer Electronics',
        slug: 'consumer-electronics',
        description: 'Televisions, audio systems, personal devices, and accessories that keep customers connected.',
    },
    {
        name: 'Garden Equipment',
        slug: 'garden-equipment',
        description: 'Power tools, watering systems, and maintenance essentials for year-round garden care.',
    },
    {
        name: 'Outdoor Furniture',
        slug: 'outdoor-furniture',
        description: 'Weather-ready seating, dining sets, and shade solutions for patios, balconies, and terraces.',
    },
    {
        name: 'Home Furniture',
        slug: 'home-furniture',
        description: 'Living room, bedroom, and storage pieces curated for comfort and contemporary style.',
    },
    {
        name: 'Kitchen & Dining',
        slug: 'kitchen-and-dining',
        description: 'Cookware, tableware, and countertop helpers that streamline meal prep and hosting.',
    },
    {
        name: 'Smart Home & IoT',
        slug: 'smart-home-and-iot',
        description: 'Connected devices, sensors, and hubs that automate lighting, security, and energy management.',
    },
    {
        name: 'Home Office Essentials',
        slug: 'home-office-essentials',
        description: 'Ergonomic desks, seating, and productivity accessories for a focused workspace.',
    },
    {
        name: 'Lighting & Fixtures',
        slug: 'lighting-and-fixtures',
        description: 'Indoor and outdoor lighting solutions, from energy-efficient bulbs to statement fixtures.',
    },
];

export default function Products({ categories = [] }: ProductsPageProps) {
    const items = categories.length > 0 ? categories : fallbackCategories;

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
                    {items.map((category) => (
                        <div
                            key={category.slug ?? category.name}
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
                                href={category.slug ? `/products#${category.slug}` : '#'}
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
