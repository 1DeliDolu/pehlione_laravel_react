import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/layouts/site-layout';

interface OrderItemSummary {
    id: number;
    name: string;
    quantity: number;
    unit_price: number;
    total_price: number;
    sku?: string | null;
    size?: string | null;
}

interface OrderSummaryProps {
    order: {
        id: number;
        status: string;
        payment_status: string;
        payment_method: string;
        total: number;
        subtotal: number;
        discount_total: number;
        shipping_total: number;
        tax_total: number;
        currency: string;
        shipping_address: Record<string, string | null>;
        billing_address: Record<string, string | null>;
        placed_at?: string | null;
        items: OrderItemSummary[];
        discount_code?: string | null;
    };
}

export default function CheckoutConfirmation({ order }: OrderSummaryProps) {
    const placedAt = order.placed_at ? new Date(order.placed_at) : null;

    return (
        <SiteLayout>
            <Head title={`Order #${order.id}`} />
            <section className="space-y-8">
                <header className="space-y-2">
                    <h1 className="text-3xl font-semibold tracking-tight">Order confirmed</h1>
                    <p className="text-sm text-neutral-600 dark:text-neutral-300">
                        Thank you! We&apos;ve started preparing order #{order.id}. A confirmation email is on the way.
                    </p>
                </header>

                <div className="grid gap-8 lg:grid-cols-[2fr_1fr]">
                    <div className="space-y-6 rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                        <div className="flex items-center justify-between">
                            <div>
                                <p className="text-sm text-neutral-500 dark:text-neutral-400">Order number</p>
                                <p className="text-xl font-semibold text-neutral-900 dark:text-neutral-50">#{order.id}</p>
                            </div>
                            <div className="text-right">
                                <p className="text-sm text-neutral-500 dark:text-neutral-400">Placed</p>
                                <p className="text-sm font-semibold text-neutral-900 dark:text-neutral-100">
                                    {placedAt ? placedAt.toLocaleString() : 'Just now'}
                                </p>
                            </div>
                        </div>

                        <section className="space-y-3">
                            <h2 className="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Items</h2>
                            <div className="space-y-3">
                                {order.items.map((item) => (
                                    <div key={item.id} className="rounded-2xl border border-neutral-200 p-4 dark:border-neutral-800">
                                        <div className="flex items-start justify-between gap-3">
                                            <div>
                                                <p className="font-semibold text-neutral-900 dark:text-neutral-100">{item.name}</p>
                                                <p className="text-sm text-neutral-600 dark:text-neutral-300">
                                                    Qty {item.quantity} · {formatCurrency(item.unit_price, order.currency)}
                                                </p>
                                                {item.sku && (
                                                    <p className="text-xs text-neutral-500 dark:text-neutral-400">SKU: {item.sku}</p>
                                                )}
                                                {item.size && (
                                                    <p className="text-xs text-neutral-500 dark:text-neutral-400">Size: {item.size}</p>
                                                )}
                                            </div>
                                            <div className="text-sm font-semibold text-neutral-900 dark:text-neutral-100">
                                                {formatCurrency(item.total_price, order.currency)}
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </section>

                        <section className="grid gap-4 md:grid-cols-2">
                            <AddressCard title="Shipping address" address={order.shipping_address} />
                            <AddressCard title="Billing address" address={order.billing_address} />
                        </section>
                    </div>

                    <aside className="space-y-6 rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                        <h2 className="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Payment summary</h2>
                        <dl className="space-y-2 text-sm text-neutral-600 dark:text-neutral-300">
                            <div className="flex justify-between">
                                <dt>Subtotal</dt>
                                <dd>{formatCurrency(order.subtotal, order.currency)}</dd>
                            </div>
                            <div className="flex justify-between">
                                <dt>Discounts</dt>
                                <dd>-{formatCurrency(order.discount_total, order.currency)}</dd>
                            </div>
                            <div className="flex justify-between">
                                <dt>Shipping</dt>
                                <dd>{formatCurrency(order.shipping_total, order.currency)}</dd>
                            </div>
                            <div className="flex justify-between">
                                <dt>Tax</dt>
                                <dd>{formatCurrency(order.tax_total, order.currency)}</dd>
                            </div>
                        </dl>

                        <div className="flex items-center justify-between text-base font-semibold text-neutral-900 dark:text-neutral-100">
                            <span>Total</span>
                            <span>{formatCurrency(order.total, order.currency)}</span>
                        </div>

                        <div className="space-y-2 rounded-2xl border border-neutral-200 p-4 text-sm text-neutral-600 dark:border-neutral-800 dark:text-neutral-300">
                            <p>
                                Payment method: <strong>{formatMethod(order.payment_method)}</strong>
                            </p>
                            <p>Status: <strong>{formatStatus(order.payment_status)}</strong></p>
                            {order.discount_code && (
                                <p>Discount code: <strong>{order.discount_code}</strong></p>
                            )}
                        </div>

                        <Link
                            href="/products"
                            className="block rounded-full bg-neutral-200 px-4 py-2 text-center text-sm font-semibold text-neutral-800 transition hover:bg-neutral-300 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700"
                        >
                            Continue shopping
                        </Link>
                    </aside>
                </div>
            </section>
        </SiteLayout>
    );
}

function AddressCard({ title, address }: { title: string; address: Record<string, string | null> }) {
    return (
        <div className="rounded-2xl border border-neutral-200 p-4 dark:border-neutral-800">
            <h3 className="text-sm font-semibold text-neutral-900 dark:text-neutral-100">{title}</h3>
            <div className="mt-2 text-sm text-neutral-600 dark:text-neutral-300">
                <p>
                    {address.first_name ?? ''} {address.last_name ?? ''}
                </p>
                {address.company && <p>{address.company}</p>}
                <p>{address.line1}</p>
                {address.line2 && <p>{address.line2}</p>}
                <p>
                    {address.postal_code} {address.city}
                </p>
                <p>{address.country}</p>
                {address.phone && <p className="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{address.phone}</p>}
            </div>
        </div>
    );
}

function formatCurrency(value: number, currency: string): string {
    return new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
    }).format(value);
}

function formatMethod(method: string): string {
    return method
        .split('_')
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(' ');
}

function formatStatus(status: string): string {
    return status.charAt(0).toUpperCase() + status.slice(1);
}
