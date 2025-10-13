import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/layouts/site-layout';
import { show as productShow } from '@/routes/products';

interface ProductSummary {
    id?: number;
    name: string;
    slug?: string;
    sku?: string;
    summary?: string | null;
    description?: string | null;
    size_profile?: string | null;
    available_sizes?: string[] | null;
    material_profile?: string | null;
    attribute_tags?: string[] | null;
    sustainability_notes?: string[] | null;
    care_instructions?: string[] | null;
    price?: number | null;
    currency?: string | null;
    stock_status?: string | null;
    stock_quantity?: number | null;
    lead_time_days?: number | null;
    energy_label?: string | null;
    metadata?: Record<string, unknown> | null;
    images?: string[] | null;
}

interface CategorySummary {
    id?: number;
    name: string;
    slug?: string;
    description?: string | null;
    products?: ProductSummary[];
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
        products: [
            {
                name: 'Sample Washer',
                summary: 'Placeholder appliance, replace after seeding.',
                attribute_tags: ['energy-efficient'],
                available_sizes: ['Standard'],
                stock_status: 'in_stock',
                images: ['/products/placeholders/placeholder-1.png'],
            },
        ],
    },
    {
        name: 'Consumer Electronics',
        slug: 'consumer-electronics',
        description: 'Televisions, audio systems, personal devices, and accessories that keep customers connected.',
        products: [],
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
        products: [],
    },
];

export default function Products({ categories = [] }: ProductsPageProps) {
    const items = categories.length > 0 ? categories : fallbackCategories;

    const formatPrice = (value?: number | null, currency?: string | null) => {
        if (value === undefined || value === null) {
            return null;
        }

        const formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency ?? 'EUR',
            minimumFractionDigits: 2,
        });

        return formatter.format(value);
    };

    return (
        <SiteLayout>
            <Head title="Products" />
            <section className="space-y-8">
                <div className="space-y-4">
                    <h1 className="text-3xl font-semibold tracking-tight">Product Collections</h1>
                    <p className="max-w-3xl text-neutral-600 dark:text-neutral-300">
                        Browse curated product groups sourced from the catalogue. Each category surfaces stocked items, sustainability notes, and sizing profiles that will later power the order pipeline.
                    </p>
                </div>
                <div className="space-y-12">
                    {items.map((category) => (
                        <div key={category.slug ?? category.name} id={category.slug} className="space-y-6">
                            <div className="space-y-2">
                                <h2 className="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-100">
                                    {category.name}
                                </h2>
                                {category.description && (
                                    <p className="max-w-3xl text-sm text-neutral-600 dark:text-neutral-300">
                                        {category.description}
                                    </p>
                                )}
                            </div>

                            <div className="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                                {(category.products ?? []).map((product) => {
                                    const priceLabel = formatPrice(product.price, product.currency);
                                    const primaryImage = product.images?.[0] ?? '/products/placeholders/placeholder-1.png';

                                    return (
                                        <div
                                            key={product.slug ?? product.name}
                                            className="flex h-full flex-col justify-between overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm transition hover:border-amber-500 hover:shadow-md dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-amber-400"
                                        >
                                            <div className="space-y-3 p-5">
                                                <div className="overflow-hidden rounded-xl border border-neutral-200/80 bg-neutral-100 dark:border-neutral-700/80 dark:bg-neutral-900">
                                                    <img
                                                        src={primaryImage}
                                                        alt={`${product.name} preview`}
                                                        className="aspect-[4/3] w-full object-cover"
                                                        loading="lazy"
                                                    />
                                                </div>
                                                <div className="space-y-1">
                                                    <div className="text-xs uppercase tracking-[0.2em] text-amber-600 dark:text-amber-400">
                                                        {product.sku ?? 'SKU TBD'}
                                                    </div>
                                                    <h3 className="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                                        {product.name}
                                                    </h3>
                                                </div>
                                                {product.summary && (
                                                    <p className="text-sm text-neutral-600 dark:text-neutral-300">
                                                        {product.summary}
                                                    </p>
                                                )}
                                                {product.attribute_tags && product.attribute_tags.length > 0 && (
                                                    <div className="flex flex-wrap gap-2">
                                                        {product.attribute_tags.map((tag) => (
                                                            <span
                                                                key={tag}
                                                                className="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300"
                                                            >
                                                                {tag}
                                                            </span>
                                                        ))}
                                                    </div>
                                                )}
                                                <dl className="space-y-2 text-sm text-neutral-600 dark:text-neutral-300">
                                                    {priceLabel && (
                                                        <div className="flex justify-between">
                                                            <dt className="font-medium text-neutral-700 dark:text-neutral-200">Price</dt>
                                                            <dd>{priceLabel}</dd>
                                                        </div>
                                                    )}
                                                    {product.available_sizes && product.available_sizes.length > 0 && (
                                                        <div>
                                                            <dt className="font-medium text-neutral-700 dark:text-neutral-200">Sizes</dt>
                                                            <dd className="mt-1 flex flex-wrap gap-2">
                                                                {product.available_sizes.map((size) => (
                                                                    <span
                                                                        key={size}
                                                                        className="inline-flex items-center rounded-md border border-neutral-200 px-2 py-0.5 text-xs font-medium text-neutral-700 dark:border-neutral-700 dark:text-neutral-200"
                                                                    >
                                                                        {size}
                                                                    </span>
                                                                ))}
                                                            </dd>
                                                        </div>
                                                    )}
                                                    <div className="flex justify-between">
                                                        <dt className="font-medium text-neutral-700 dark:text-neutral-200">Stock</dt>
                                                        <dd className="capitalize">{product.stock_status ?? 'pending'}</dd>
                                                    </div>
                                                    {typeof product.stock_quantity === 'number' && (
                                                        <div className="flex justify-between">
                                                            <dt className="font-medium text-neutral-700 dark:text-neutral-200">Qty</dt>
                                                            <dd>{product.stock_quantity}</dd>
                                                        </div>
                                                    )}
                                                    {product.lead_time_days && (
                                                        <div className="flex justify-between">
                                                            <dt className="font-medium text-neutral-700 dark:text-neutral-200">Lead Time</dt>
                                                            <dd>{product.lead_time_days} days</dd>
                                                        </div>
                                                    )}
                                                    {product.energy_label && (
                                                        <div className="flex justify-between">
                                                            <dt className="font-medium text-neutral-700 dark:text-neutral-200">Energy Label</dt>
                                                            <dd>{product.energy_label}</dd>
                                                        </div>
                                                    )}
                                                </dl>
                                            </div>
                                            <div className="mt-4 space-y-2 border-t border-neutral-200 px-5 pb-5 pt-4 text-sm text-neutral-600 dark:border-neutral-700 dark:text-neutral-300">
                                                {product.sustainability_notes && product.sustainability_notes.length > 0 && (
                                                    <div>
                                                        <h4 className="font-medium text-neutral-800 dark:text-neutral-100">Sustainability</h4>
                                                        <ul className="mt-1 list-disc space-y-1 pl-5">
                                                            {product.sustainability_notes.map((note) => (
                                                                <li key={note}>{note}</li>
                                                            ))}
                                                        </ul>
                                                    </div>
                                                )}
                                                {product.care_instructions && product.care_instructions.length > 0 && (
                                                    <div>
                                                        <h4 className="font-medium text-neutral-800 dark:text-neutral-100">Care</h4>
                                                        <ul className="mt-1 list-disc space-y-1 pl-5">
                                                            {product.care_instructions.map((instruction) => (
                                                                <li key={instruction}>{instruction}</li>
                                                            ))}
                                                        </ul>
                                                    </div>
                                                )}
                                                <div>
                                                    <Link
                                                        href={product.slug ? productShow({ product: product.slug }) : '#'}
                                                        className="mt-3 inline-flex items-center justify-center rounded-full bg-amber-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-amber-500 dark:bg-amber-500 dark:hover:bg-amber-400"
                                                    >
                                                        View product
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })}
                                {(category.products ?? []).length === 0 && (
                                    <div className="rounded-2xl border border-dashed border-neutral-300 bg-neutral-50 p-6 text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
                                        Products for this category will appear here once catalogued.
                                    </div>
                                )}
                            </div>
                        </div>
                    ))}
                </div>
            </section>
        </SiteLayout>
    );
}
