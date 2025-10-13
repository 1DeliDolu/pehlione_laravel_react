import { Head, Link } from '@inertiajs/react';
import SiteLayout from '@/layouts/site-layout';

export default function CheckoutEmpty() {
    return (
        <SiteLayout>
            <Head title="Checkout" />
            <section className="mx-auto max-w-xl space-y-6 rounded-3xl border border-neutral-200 bg-white p-10 text-center shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <h1 className="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">Cart is empty</h1>
                <p className="text-sm text-neutral-600 dark:text-neutral-300">
                    Add products to your cart before you can start the checkout process.
                </p>
                <div className="flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <Link
                        href="/products"
                        className="rounded-full bg-amber-500 px-6 py-3 text-sm font-semibold text-white transition hover:bg-amber-400"
                    >
                        Browse products
                    </Link>
                    <Link
                        href="/cart"
                        className="rounded-full border border-neutral-300 px-6 py-3 text-sm font-semibold text-neutral-700 transition hover:border-neutral-400 dark:border-neutral-700 dark:text-neutral-200"
                    >
                        Go to cart
                    </Link>
                </div>
            </section>
        </SiteLayout>
    );
}

