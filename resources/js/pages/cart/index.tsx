import { Head, Link, useForm } from '@inertiajs/react';
import { useEffect, type ChangeEvent } from 'react';
import SiteLayout from '@/layouts/site-layout';
import { show as productShow } from '@/routes/products';
import cartRoutes from '@/routes/cart';

interface CartProductSummary {
    id: number;
    product: {
        id: number;
        name: string;
        slug?: string | null;
        thumbnail?: string | null;
    };
    sku: string;
    size?: string | null;
    quantity: number;
    unit_price: number;
    total_price: number;
    attribute_tags: string[];
}

interface CartPageProps {
    cart: {
        id: number;
        status: string;
        subtotal: number;
        currency: string;
        items: CartProductSummary[];
    };
}

export default function CartIndex({ cart }: CartPageProps) {
    const hasItems = cart.items.length > 0;

    return (
        <SiteLayout>
            <Head title="Cart" />
            <section className="space-y-8">
                <header className="space-y-2">
                    <h1 className="text-3xl font-semibold tracking-tight">Your Cart</h1>
                    <p className="text-sm text-neutral-600 dark:text-neutral-300">
                        Review selected products, adjust quantities, or remove items before you confirm the order.
                    </p>
                </header>

                {!hasItems && (
                    <div className="rounded-3xl border border-dashed border-neutral-300 bg-neutral-50 p-10 text-center text-sm text-neutral-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-400">
                        Your cart is empty. Browse products and add items to start planning an order.
                    </div>
                )}

                {hasItems && (
                    <div className="grid gap-8 lg:grid-cols-[2fr_1fr]">
                        <div className="space-y-4">
                            {cart.items.map((item) => (
                                <CartItemCard key={item.id} item={item} />
                            ))}
                        </div>

                        <aside className="space-y-4 rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                            <h2 className="text-lg font-semibold text-neutral-800 dark:text-neutral-100">Order summary</h2>
                            <dl className="space-y-3 text-sm text-neutral-600 dark:text-neutral-300">
                                <div className="flex justify-between">
                                    <dt>Subtotal</dt>
                                    <dd>{formatCurrency(cart.subtotal, cart.currency)}</dd>
                                </div>
                                <div className="flex justify-between">
                                    <dt>Shipping</dt>
                                    <dd className="text-neutral-400">Calculated during fulfilment</dd>
                                </div>
                                <div className="flex justify-between text-base font-semibold text-neutral-900 dark:text-neutral-100">
                                    <dt>Total</dt>
                                    <dd>{formatCurrency(cart.subtotal, cart.currency)}</dd>
                                </div>
                            </dl>
                            <button
                                type="button"
                                className="w-full rounded-full bg-amber-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-amber-500 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-amber-500 dark:hover:bg-amber-400"
                                disabled
                            >
                                Checkout (coming soon)
                            </button>
                            <p className="text-xs text-neutral-500 dark:text-neutral-400">
                                Checkout is under construction. For now use this summary to coordinate fulfilment with the team.
                            </p>
                        </aside>
                    </div>
                )}
            </section>
        </SiteLayout>
    );
}

function CartItemCard({ item }: { item: CartProductSummary }) {
    const quantityForm = useForm({ quantity: item.quantity });
    const removeForm = useForm({});

    const productUrl = item.product.slug ? productShow({ product: item.product.slug }).url : '#';

    useEffect(() => {
        quantityForm.setData('quantity', item.quantity);
    }, [item.quantity]);

    const applyQuantity = (next: number) => {
        const clamped = Math.max(1, Math.min(50, next));

        if (clamped === item.quantity || quantityForm.processing) {
            quantityForm.setData('quantity', clamped);
            return;
        }

        quantityForm.setData('quantity', clamped);
        quantityForm.patch(cartRoutes.items.update({ item: item.id }).url, {
            preserveScroll: true,
        });
    };

    const handleQuantityChange = (event: ChangeEvent<HTMLInputElement>) => {
        const value = Number(event.target.value);

        if (Number.isNaN(value)) {
            quantityForm.setData('quantity', item.quantity);
            return;
        }

        applyQuantity(value);
    };

    const currentQuantity = quantityForm.data.quantity ?? item.quantity;
    const derivedTotal = item.unit_price * currentQuantity;

    return (
        <div className="flex flex-col gap-4 rounded-3xl border border-neutral-200 bg-white p-5 shadow-sm transition hover:border-amber-400 hover:shadow-md dark:border-neutral-800 dark:bg-neutral-900">
            <div className="flex gap-4">
                <img
                    src={item.product.thumbnail ?? '/products/placeholders/placeholder-1.png'}
                    alt={item.product.name}
                    className="h-24 w-24 rounded-2xl object-cover"
                />
                <div className="flex flex-1 flex-col justify-between">
                    <div>
                        <div className="text-xs uppercase tracking-[0.2em] text-amber-600 dark:text-amber-400">{item.sku}</div>
                        <Link
                            href={productUrl}
                            className="text-lg font-semibold text-neutral-900 transition hover:text-amber-600 dark:text-neutral-100"
                        >
                            {item.product.name}
                        </Link>
                        {item.size && (
                            <p className="text-xs text-neutral-500 dark:text-neutral-400">Size: {item.size}</p>
                        )}
                        {item.attribute_tags.length > 0 && (
                            <div className="mt-2 flex flex-wrap gap-2">
                                {item.attribute_tags.map((tag) => (
                                    <span
                                        key={tag}
                                        className="inline-flex items-center rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                                    >
                                        {tag}
                                    </span>
                                ))}
                            </div>
                        )}
                    </div>
                    <div className="flex flex-wrap items-center gap-3 text-sm text-neutral-600 dark:text-neutral-300">
                        <span>Unit: {formatCurrency(item.unit_price)}</span>
                        <span className="font-semibold text-neutral-900 dark:text-neutral-100">
                            {formatCurrency(derivedTotal)}
                        </span>
                    </div>
                </div>
            </div>

            <div className="flex flex-wrap items-center justify-between gap-3">
                <div className="flex items-center gap-2">
                    <label className="text-xs font-semibold uppercase tracking-widest text-neutral-500 dark:text-neutral-400">
                        Quantity
                    </label>
                    <input
                        type="number"
                        min={1}
                        max={50}
                        value={currentQuantity}
                        onChange={handleQuantityChange}
                        className="h-10 w-20 rounded-full border border-neutral-300 px-3 text-sm focus:border-amber-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-900"
                        disabled={quantityForm.processing}
                    />
                </div>

                <button
                    type="button"
                    onClick={() =>
                        removeForm.delete(cartRoutes.items.destroy({ item: item.id }).url, {
                            preserveScroll: true,
                        })
                    }
                    className="rounded-full border border-red-500 px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-red-500 transition hover:bg-red-500 hover:text-white"
                    disabled={removeForm.processing || quantityForm.processing}
                >
                    Remove
                </button>
            </div>
        </div>
    );
}

function formatCurrency(value: number, currency = 'EUR') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
    }).format(value);
}
