import { Head, Link, useForm } from '@inertiajs/react';
import { FormEvent, useCallback, useEffect, useMemo, useState } from 'react';
import SiteLayout from '@/layouts/site-layout';

interface CartItemSummary {
    id: number;
    name: string;
    sku?: string | null;
    size?: string | null;
    quantity: number;
    unit_price: number;
    total_price: number;
    thumbnail?: string | null;
}

interface AddressSummary {
    id: number;
    label?: string | null;
    first_name: string;
    last_name: string;
    company?: string | null;
    line1: string;
    line2?: string | null;
    postal_code: string;
    city: string;
    state?: string | null;
    country: string;
    phone?: string | null;
    is_default_shipping: boolean;
    is_default_billing: boolean;
}

interface PaymentMethod {
    id: string;
    label: string;
    description?: string;
}

interface Totals {
    currency: string;
    subtotal: number;
    discount_total: number;
    shipping_total: number;
    tax_total: number;
    total: number;
}

interface CheckoutPageProps {
    cart: {
        id: number;
        currency: string;
        items: CartItemSummary[];
    };
    addresses: AddressSummary[];
    defaults: {
        shipping_address_id?: number | null;
        billing_address_id?: number | null;
    };
    paymentMethods: PaymentMethod[];
    shippingConfig: {
        domestic_country: string;
        domestic_flat_rate: number;
        international_flat_rate: number;
        domestic_free_threshold: number;
        default_method: string;
    };
    initialTotals: Totals;
}

type AddressFormState = {
    label: string;
    first_name: string;
    last_name: string;
    company: string;
    line1: string;
    line2: string;
    postal_code: string;
    city: string;
    state: string;
    country: string;
    phone: string;
    set_as_default_shipping?: boolean;
    set_as_default_billing?: boolean;
};

const defaultAddressState = (country: string): AddressFormState => ({
    label: '',
    first_name: '',
    last_name: '',
    company: '',
    line1: '',
    line2: '',
    postal_code: '',
    city: '',
    state: '',
    country,
    phone: '',
    set_as_default_shipping: false,
    set_as_default_billing: false,
});

export default function CheckoutIndex({
    cart,
    addresses,
    defaults,
    paymentMethods,
    shippingConfig,
    initialTotals,
}: CheckoutPageProps) {
    const defaultCountry = shippingConfig.domestic_country ?? 'DE';
    const [totals, setTotals] = useState<Totals>(initialTotals);
    const [isCalculating, setIsCalculating] = useState(false);
    const [discountError, setDiscountError] = useState<string | null>(null);

    const [shippingMode, setShippingMode] = useState<'existing' | 'new'>(
        addresses.length > 0 && defaults.shipping_address_id ? 'existing' : addresses.length > 0 ? 'existing' : 'new'
    );
    const [selectedShippingId, setSelectedShippingId] = useState<number | null>(defaults.shipping_address_id ?? (addresses[0]?.id ?? null));
    const [shippingDraft, setShippingDraft] = useState<AddressFormState>(() => {
        const draft = defaultAddressState(defaultCountry);
        draft.set_as_default_shipping = addresses.length === 0;
        return draft;
    });

    const [billingSameAsShipping, setBillingSameAsShipping] = useState<boolean>(!defaults.billing_address_id);
    const [billingMode, setBillingMode] = useState<'existing' | 'new'>(defaults.billing_address_id ? 'existing' : 'new');
    const [selectedBillingId, setSelectedBillingId] = useState<number | null>(defaults.billing_address_id ?? null);
    const [billingDraft, setBillingDraft] = useState<AddressFormState>(() => defaultAddressState(defaultCountry));

    const defaultPaymentMethod = paymentMethods[0]?.id ?? '';

    const checkoutForm = useForm({
        shipping_address: shippingMode === 'existing' && selectedShippingId ? { id: selectedShippingId } : { ...shippingDraft, id: null },
        billing_address: billingSameAsShipping
            ? { same_as_shipping: true }
            : billingMode === 'existing' && selectedBillingId
              ? { id: selectedBillingId, same_as_shipping: false }
              : { ...billingDraft, same_as_shipping: false, id: null },
        payment_method: defaultPaymentMethod,
        discount_code: '',
        notes: '',
    });

    const { data, setData, post, processing, errors, setError, clearErrors } = checkoutForm;

    const shippingFieldErrors = useMemo(
        () => mapFieldErrors(errors as Record<string, string>, 'shipping_address'),
        [errors]
    );
    const billingFieldErrors = useMemo(
        () => mapFieldErrors(errors as Record<string, string>, 'billing_address'),
        [errors]
    );

    const shippingCountry = useMemo(() => {
        if (shippingMode === 'existing' && selectedShippingId) {
            return addresses.find((address) => address.id === selectedShippingId)?.country ?? defaultCountry;
        }

        return shippingDraft.country || defaultCountry;
    }, [addresses, defaultCountry, selectedShippingId, shippingDraft.country, shippingMode]);

    useEffect(() => {
        if (shippingMode === 'existing') {
            setData('shipping_address', { id: selectedShippingId });
        } else {
            setData('shipping_address', {
                ...shippingDraft,
                id: null,
            });
        }
    }, [setData, selectedShippingId, shippingDraft, shippingMode]);

    useEffect(() => {
        if (billingSameAsShipping) {
            setData('billing_address', { same_as_shipping: true });
            return;
        }

        if (billingMode === 'existing') {
            setData('billing_address', {
                id: selectedBillingId,
                same_as_shipping: false,
            });
        } else {
            setData('billing_address', {
                ...billingDraft,
                same_as_shipping: false,
                id: null,
            });
        }
    }, [billingDraft, billingMode, billingSameAsShipping, selectedBillingId, setData]);

    const discountCode = data.discount_code;

    const refreshTotals = useCallback(async () => {
        if (!shippingCountry) {
            return;
        }

        setIsCalculating(true);

        try {
            const response = await fetch('/checkout/preview', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN':
                        document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    shipping_address: {
                        country: shippingCountry,
                    },
                    discount_code: discountCode || null,
                }),
            });

            if (!response.ok) {
                if (response.status === 422) {
                    const payload = await response.json();
                    const message =
                        payload?.errors?.discount_code?.[0] ??
                        payload?.errors?.cart?.[0] ??
                        payload?.errors?.setup?.[0] ??
                        'Unable to update totals.';
                    setDiscountError(message);
                    setError('discount_code', message);
                    return;
                }

                let fallbackMessage = 'Unable to update totals. Please try again.';
                try {
                    const payload = await response.json();
                    fallbackMessage =
                        payload?.message ??
                        payload?.errors?.setup?.[0] ??
                        fallbackMessage;
                } catch (jsonError) {
                    console.error(jsonError);
                }

                setDiscountError(fallbackMessage);
                setError('discount_code', fallbackMessage);
                return;
            }

            const json = (await response.json()) as { totals: Totals };
            setTotals(json.totals);
            setDiscountError(null);
            clearErrors('discount_code');
        } catch (error) {
            console.error(error);
        } finally {
            setIsCalculating(false);
        }
    }, [clearErrors, discountCode, setError, shippingCountry]);

    useEffect(() => {
        const timeout = setTimeout(() => {
            void refreshTotals();
        }, 300);

        return () => clearTimeout(timeout);
    }, [refreshTotals]);

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        post('/checkout');
    };

    const renderAddressCard = (address: AddressSummary) => (
        <div
            key={address.id}
            className="rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
        >
            <div className="flex items-start justify-between gap-3">
                <div>
                    <p className="font-semibold text-neutral-900 dark:text-neutral-100">
                        {address.label ?? `${address.first_name} ${address.last_name}`}
                    </p>
                    <p className="text-sm text-neutral-600 dark:text-neutral-300">
                        {address.line1}
                        {address.line2 && (
                            <>
                                <br />
                                {address.line2}
                            </>
                        )}
                        <br />
                        {address.postal_code} {address.city}
                        <br />
                        {address.country}
                    </p>
                    {address.phone && (
                        <p className="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{address.phone}</p>
                    )}
                </div>
                <div className="text-xs uppercase tracking-[0.2em] text-amber-600 dark:text-amber-400">
                    {address.is_default_shipping && 'Default Shipping'}
                    {!address.is_default_shipping && address.is_default_billing && 'Default Billing'}
                </div>
            </div>
        </div>
    );

    const currency = totals.currency ?? cart.currency;

    return (
        <SiteLayout>
            <Head title="Checkout" />
            <section className="space-y-8">
                <header className="space-y-2">
                    <h1 className="text-3xl font-semibold tracking-tight">Checkout</h1>
                    <p className="text-sm text-neutral-600 dark:text-neutral-300">
                        Confirm shipping, payment, and review your order before placing it.
                    </p>
                </header>

        <form onSubmit={handleSubmit} className="grid gap-8 lg:grid-cols-[2fr_1fr]">
                    <div className="space-y-8">
                        <section className="space-y-4 rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                            <div className="flex items-center justify-between">
                                <h2 className="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                    1. Shipping address
                                </h2>
                                {addresses.length > 0 && (
                                    <div className="flex gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-400">
                                        <button
                                            type="button"
                                            onClick={() => setShippingMode('existing')}
                                            className={`rounded-full px-3 py-1 ${
                                                shippingMode === 'existing'
                                                    ? 'bg-amber-500 text-white'
                                                    : 'bg-neutral-200 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300'
                                            }`}
                                        >
                                            Saved
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => setShippingMode('new')}
                                            className={`rounded-full px-3 py-1 ${
                                                shippingMode === 'new'
                                                    ? 'bg-amber-500 text-white'
                                                    : 'bg-neutral-200 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300'
                                            }`}
                                        >
                                            New
                                        </button>
                                    </div>
                                )}
                            </div>

                            {shippingMode === 'existing' && addresses.length > 0 ? (
                                <div className="space-y-3">
                                    {addresses.map((address) => (
                                        <label key={address.id} className="flex cursor-pointer gap-3">
                                            <input
                                                type="radio"
                                                name="shipping_address_id"
                                                value={address.id}
                                                checked={selectedShippingId === address.id}
                                                onChange={() => setSelectedShippingId(address.id)}
                                                className="mt-1 h-4 w-4"
                                            />
                                            {renderAddressCard(address)}
                                        </label>
                                    ))}
                                    <p className="text-xs text-neutral-500 dark:text-neutral-400">
                                        Need another destination? Switch to “New” above to add it.
                                    </p>
                                </div>
                            ) : (
                                <AddressForm
                                    state={shippingDraft}
                                    onChange={setShippingDraft}
                                    errors={shippingFieldErrors}
                                    allowDefaultOptions
                                    type="shipping"
                                />
                            )}
                        </section>

                        <section className="space-y-4 rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                            <div className="flex items-center justify-between">
                                <h2 className="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                    2. Billing address
                                </h2>
                                <label className="flex items-center gap-2 text-xs uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-400">
                                    <input
                                        type="checkbox"
                                        checked={billingSameAsShipping}
                                        onChange={(event) => {
                                            setBillingSameAsShipping(event.target.checked);
                                        }}
                                        className="h-4 w-4"
                                    />
                                    Same as shipping
                                </label>
                            </div>

                            {!billingSameAsShipping && (
                                <div className="space-y-4">
                                    {addresses.length > 0 && (
                                        <div className="flex gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-neutral-500 dark:text-neutral-400">
                                            <button
                                                type="button"
                                                onClick={() => setBillingMode('existing')}
                                                className={`rounded-full px-3 py-1 ${
                                                    billingMode === 'existing'
                                                        ? 'bg-amber-500 text-white'
                                                        : 'bg-neutral-200 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300'
                                                }`}
                                            >
                                                Saved
                                            </button>
                                            <button
                                                type="button"
                                                onClick={() => setBillingMode('new')}
                                                className={`rounded-full px-3 py-1 ${
                                                    billingMode === 'new'
                                                        ? 'bg-amber-500 text-white'
                                                        : 'bg-neutral-200 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300'
                                                }`}
                                            >
                                                New
                                            </button>
                                        </div>
                                    )}

                                    {billingMode === 'existing' && addresses.length > 0 ? (
                                        <div className="space-y-3">
                                            {addresses.map((address) => (
                                                <label key={address.id} className="flex cursor-pointer gap-3">
                                                    <input
                                                        type="radio"
                                                        name="billing_address_id"
                                                        value={address.id}
                                                        checked={selectedBillingId === address.id}
                                                        onChange={() => setSelectedBillingId(address.id)}
                                                        className="mt-1 h-4 w-4"
                                                    />
                                                    {renderAddressCard(address)}
                                                </label>
                                            ))}
                                        </div>
                                    ) : (
                                        <AddressForm
                                            state={billingDraft}
                                            onChange={setBillingDraft}
                                            errors={billingFieldErrors}
                                            allowDefaultOptions
                                            type="billing"
                                        />
                                    )}
                                </div>
                            )}
                        </section>

                        <section className="space-y-4 rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                            <h2 className="text-lg font-semibold text-neutral-900 dark:text-neutral-100">
                                3. Payment
                            </h2>
                            <div className="space-y-3">
                                {paymentMethods.map((method) => (
                                    <label
                                        key={method.id}
                                        className={`flex cursor-pointer items-center justify-between gap-3 rounded-2xl border p-4 ${
                                            data.payment_method === method.id
                                                ? 'border-amber-500 bg-amber-50 dark:border-amber-400 dark:bg-neutral-800'
                                                : 'border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900'
                                        }`}
                                    >
                                        <div>
                                            <p className="font-semibold text-neutral-900 dark:text-neutral-100">{method.label}</p>
                                            {method.description && (
                                                <p className="text-sm text-neutral-600 dark:text-neutral-300">{method.description}</p>
                                            )}
                                        </div>
                                        <input
                                            type="radio"
                                            name="payment_method"
                                            value={method.id}
                                            checked={data.payment_method === method.id}
                                            onChange={() => setData('payment_method', method.id)}
                                            className="h-4 w-4"
                                        />
                                    </label>
                                ))}
                                {errors.payment_method && (
                                    <p className="text-sm text-red-500">{errors.payment_method}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <label className="text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                                    Discount code (optional)
                                </label>
                                <input
                                    type="text"
                                    value={data.discount_code ?? ''}
                                    onChange={(event) => setData('discount_code', event.target.value.trim().toUpperCase())}
                                    className="w-full rounded-xl border border-neutral-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-900"
                                    placeholder="Enter code"
                                />
                                {(discountError || errors.discount_code) && (
                                    <p className="text-sm text-red-500">{discountError ?? errors.discount_code}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <label className="text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                                    Order notes (optional)
                                </label>
                                <textarea
                                    value={data.notes ?? ''}
                                    onChange={(event) => setData('notes', event.target.value)}
                                    className="h-24 w-full rounded-xl border border-neutral-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-900"
                                    placeholder="Add any delivery instructions for the team…"
                                />
                                {errors.notes && <p className="text-sm text-red-500">{errors.notes}</p>}
                            </div>
                        </section>
                    </div>

                    <aside className="space-y-6 rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                        <div className="flex items-center justify-between">
                            <h2 className="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Order summary</h2>
                            {isCalculating && (
                                <span className="text-xs text-neutral-500 dark:text-neutral-400">Updating…</span>
                            )}
                        </div>

                        <div className="space-y-3">
                            {cart.items.map((item) => (
                                <div key={item.id} className="flex items-start gap-3">
                                    <img
                                        src={item.thumbnail ?? '/products/placeholders/placeholder-1.png'}
                                        alt={item.name}
                                        className="h-16 w-16 rounded-xl object-cover"
                                    />
                                    <div className="flex-1 text-sm text-neutral-600 dark:text-neutral-300">
                                        <p className="font-semibold text-neutral-900 dark:text-neutral-100">{item.name}</p>
                                        <p>
                                            Qty: {item.quantity} · {formatCurrency(item.unit_price, currency)}
                                        </p>
                                    </div>
                                    <div className="text-sm font-semibold text-neutral-900 dark:text-neutral-100">
                                        {formatCurrency(item.total_price, currency)}
                                    </div>
                                </div>
                            ))}
                        </div>

                        <dl className="space-y-2 text-sm text-neutral-600 dark:text-neutral-300">
                            <div className="flex justify-between">
                                <dt>Subtotal</dt>
                                <dd>{formatCurrency(totals.subtotal, currency)}</dd>
                            </div>
                            <div className="flex justify-between">
                                <dt>Discounts</dt>
                                <dd>-{formatCurrency(totals.discount_total, currency)}</dd>
                            </div>
                            <div className="flex justify-between">
                                <dt>Shipping</dt>
                                <dd>{formatCurrency(totals.shipping_total, currency)}</dd>
                            </div>
                            <div className="flex justify-between">
                                <dt>Tax</dt>
                                <dd>{formatCurrency(totals.tax_total, currency)}</dd>
                            </div>
                        </dl>

                        <div className="flex items-center justify-between text-base font-semibold text-neutral-900 dark:text-neutral-100">
                            <span>Total</span>
                            <span>{formatCurrency(totals.total, currency)}</span>
                        </div>

                        <button
                            type="submit"
                            className="w-full rounded-full bg-amber-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-amber-500 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-amber-500 dark:hover:bg-amber-400"
                            disabled={processing}
                        >
                            {processing ? 'Processing…' : 'Place order'}
                        </button>

                        <div className="text-xs text-neutral-500 dark:text-neutral-400">
                            <p>By placing your order you accept our terms and consent to receive fulfilment updates.</p>
                            <p className="mt-2">
                                <Link href="/cart" className="text-amber-600 hover:underline dark:text-amber-400">
                                    Return to cart
                                </Link>
                            </p>
                        </div>
                    </aside>
                </form>
            </section>
        </SiteLayout>
    );
}

function AddressForm({
    state,
    onChange,
    errors,
    allowDefaultOptions,
    type,
}: {
    state: AddressFormState;
    onChange: (state: AddressFormState) => void;
    errors: Record<string, string>;
    allowDefaultOptions?: boolean;
    type: 'shipping' | 'billing';
}) {
    const update = (key: keyof AddressFormState, value: string | boolean) => {
        onChange({
            ...state,
            [key]: value,
        });
    };

    return (
        <div className="space-y-3">
            <div className="grid gap-3 sm:grid-cols-2">
                <Field
                    label="First name"
                    value={state.first_name}
                    onChange={(value) => update('first_name', value)}
                    error={errors.first_name}
                />
                <Field
                    label="Last name"
                    value={state.last_name}
                    onChange={(value) => update('last_name', value)}
                    error={errors.last_name}
                />
            </div>
            <Field
                label="Company (optional)"
                value={state.company}
                onChange={(value) => update('company', value)}
                error={errors.company}
            />
            <Field
                label="Street address"
                value={state.line1}
                onChange={(value) => update('line1', value)}
                error={errors.line1}
            />
            <Field
                label="Apartment, suite, etc. (optional)"
                value={state.line2}
                onChange={(value) => update('line2', value)}
                error={errors.line2}
            />
            <div className="grid gap-3 sm:grid-cols-3">
                <Field
                    label="Postal code"
                    value={state.postal_code}
                    onChange={(value) => update('postal_code', value)}
                    error={errors.postal_code}
                />
                <Field
                    label="City"
                    value={state.city}
                    onChange={(value) => update('city', value)}
                    error={errors.city}
                />
                <Field
                    label="State / Region"
                    value={state.state}
                    onChange={(value) => update('state', value)}
                    error={errors.state}
                />
            </div>
            <div className="grid gap-3 sm:grid-cols-2">
                <Field
                    label="Country"
                    value={state.country}
                    onChange={(value) => update('country', value.toUpperCase())}
                    error={errors.country}
                    placeholder="DE"
                />
                <Field
                    label="Phone (optional)"
                    value={state.phone}
                    onChange={(value) => update('phone', value)}
                    error={errors.phone}
                />
            </div>

            {allowDefaultOptions && (
                <div className="space-y-2 rounded-2xl border border-neutral-200 p-3 text-xs text-neutral-600 dark:border-neutral-700 dark:text-neutral-300">
                    {type === 'shipping' && (
                        <label className="flex items-center gap-2">
                            <input
                                type="checkbox"
                                checked={Boolean(state.set_as_default_shipping)}
                                onChange={(event) => update('set_as_default_shipping', event.target.checked)}
                                className="h-4 w-4"
                            />
                            Make this my default shipping address
                        </label>
                    )}
                    <label className="flex items-center gap-2">
                        <input
                            type="checkbox"
                            checked={Boolean(state.set_as_default_billing)}
                            onChange={(event) => update('set_as_default_billing', event.target.checked)}
                            className="h-4 w-4"
                        />
                        Make this my default billing address
                    </label>
                </div>
            )}
        </div>
    );
}

function Field({
    label,
    value,
    onChange,
    error,
    placeholder,
}: {
    label: string;
    value: string;
    onChange: (value: string) => void;
    error?: string;
    placeholder?: string;
}) {
    return (
        <div className="space-y-1">
            <label className="text-sm font-semibold text-neutral-700 dark:text-neutral-200">
                {label}
            </label>
            <input
                value={value}
                onChange={(event) => onChange(event.target.value)}
                placeholder={placeholder}
                className="w-full rounded-xl border border-neutral-300 px-3 py-2 text-sm focus:border-amber-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-900"
            />
            {error && <p className="text-sm text-red-500">{error}</p>}
        </div>
    );
}

function mapFieldErrors(errors: Record<string, string>, prefix: string): Record<string, string> {
    const result: Record<string, string> = {};
    const prefixDot = `${prefix}.`;

    Object.entries(errors ?? {}).forEach(([key, message]) => {
        if (key.startsWith(prefixDot)) {
            result[key.substring(prefixDot.length)] = message;
        }
    });

    return result;
}

function formatCurrency(value: number, currency = 'EUR'): string {
    return new Intl.NumberFormat('de-DE', {
        style: 'currency',
        currency,
        minimumFractionDigits: 2,
    }).format(value);
}
