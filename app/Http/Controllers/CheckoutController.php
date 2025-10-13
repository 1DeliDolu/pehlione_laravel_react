<?php

namespace App\Http\Controllers;

use App\Actions\Checkout\FinalizeCheckout;
use App\Http\Requests\Checkout\StoreCheckoutRequest;
use App\Models\Cart;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Services\Checkout\CheckoutCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function index(Request $request, CheckoutCalculator $calculator): Response
    {
        $missingTables = $this->missingCheckoutTables();

        if ($missingTables !== []) {
            return Inertia::render('checkout/setup-required', [
                'missingTables' => $missingTables,
            ]);
        }

        $user = $request->user();
        $cart = $this->activeCart($user);
        $cart->loadMissing(['items.product']);

        if ($cart->items->isEmpty()) {
            return Inertia::render('checkout/empty');
        }

        $addresses = $user->addresses()->orderByDesc('is_default_shipping')->orderByDesc('is_default_billing')->get();
        $defaultShipping = $user->defaultShippingAddress() ?? $addresses->first();

        $shippingSnapshot = $defaultShipping
            ? $defaultShipping->toSnapshot()
            : [
                'country' => config('checkout.shipping.domestic_country', 'DE'),
            ];

        $totals = $calculator->calculate($cart, null, $shippingSnapshot);

        return Inertia::render('checkout/index', [
            'cart' => [
                'id' => $cart->id,
                'currency' => $cart->currency,
                'items' => $cart->items->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'size' => $item->size !== '' ? $item->size : null,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'total_price' => (float) $item->total_price,
                    'thumbnail' => $item->product?->images[0] ?? '/products/placeholders/placeholder-1.png',
                ]),
            ],
            'addresses' => $addresses->map(fn ($address) => [
                'id' => $address->id,
                'label' => $address->label,
                'first_name' => $address->first_name,
                'last_name' => $address->last_name,
                'company' => $address->company,
                'line1' => $address->line1,
                'line2' => $address->line2,
                'postal_code' => $address->postal_code,
                'city' => $address->city,
                'state' => $address->state,
                'country' => $address->country,
                'phone' => $address->phone,
                'is_default_shipping' => $address->is_default_shipping,
                'is_default_billing' => $address->is_default_billing,
            ]),
            'defaults' => [
                'shipping_address_id' => $defaultShipping?->id,
                'billing_address_id' => $user->defaultBillingAddress()?->id,
            ],
            'paymentMethods' => config('checkout.payment_methods'),
            'shippingConfig' => config('checkout.shipping'),
            'initialTotals' => $totals,
        ]);
    }

    public function preview(Request $request, CheckoutCalculator $calculator): JsonResponse
    {
        $this->guardAgainstMissingTables();

        $user = $request->user();
        $cart = $this->activeCart($user);
        $cart->loadMissing('items');

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => __('Your cart is empty.'),
            ]);
        }

        $data = $request->validate([
            'shipping_address.country' => ['required', 'string', 'size:2'],
            'discount_code' => ['nullable', 'string', 'max:50'],
        ]);

        $shippingAddress = [
            'country' => strtoupper($data['shipping_address']['country']),
        ];

        $discountCode = null;
        if (! empty($data['discount_code'])) {
            $discountCode = $this->resolveDiscountCode($data['discount_code']);
        }

        $totals = $calculator->calculate($cart, $discountCode, $shippingAddress);

        return response()->json([
            'totals' => $totals,
        ]);
    }

    public function store(
        StoreCheckoutRequest $request,
        FinalizeCheckout $finalizeCheckout
    ): RedirectResponse {
        $this->guardAgainstMissingTables();

        $user = $request->user();
        $cart = $this->activeCart($user);
        $cart->loadMissing('items');

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => __('Your cart is empty.'),
            ]);
        }

        $data = $request->validated();
        $discountCode = null;

        if (! empty($data['discount_code'])) {
            $discountCode = $this->resolveDiscountCode($data['discount_code']);
        }

        $order = $finalizeCheckout->handle($user, $cart, $data, $discountCode);

        return redirect()->route('checkout.confirmation', $order);
    }

    public function confirmation(Request $request, Order $order): Response
    {
        $user = $request->user();

        if ($order->user_id !== $user->id) {
            abort(403);
        }

        $order->loadMissing(['items', 'discountCode']);

        return Inertia::render('checkout/confirmation', [
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'total' => (float) $order->total,
                'subtotal' => (float) $order->subtotal,
                'discount_total' => (float) $order->discount_total,
                'shipping_total' => (float) $order->shipping_total,
                'tax_total' => (float) $order->tax_total,
                'currency' => $order->currency,
                'shipping_address' => $order->shipping_address,
                'billing_address' => $order->billing_address,
                'placed_at' => optional($order->placed_at)->toIso8601String(),
                'items' => $order->items->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'total_price' => (float) $item->total_price,
                    'sku' => $item->sku,
                    'size' => $item->size,
                ]),
                'discount_code' => $order->discountCode?->code,
            ],
        ]);
    }

    protected function activeCart($user): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            ['currency' => config('checkout.currency', 'EUR')]
        );
    }

    protected function resolveDiscountCode(string $code): DiscountCode
    {
        $discountCode = DiscountCode::whereRaw('LOWER(code) = ?', [mb_strtolower($code)])->first();

        if (! $discountCode || ! $discountCode->isCurrentlyActive()) {
            throw ValidationException::withMessages([
                'discount_code' => __('The discount code is not valid.'),
            ]);
        }

        return $discountCode;
    }

    /**
     * @return list<string>
     */
    protected function missingCheckoutTables(): array
    {
        static $cache;

        if ($cache !== null) {
            return $cache;
        }

        $tables = [
            'addresses',
            'discount_codes',
            'discount_redemptions',
            'orders',
            'order_items',
            'order_payments',
            'warehouse_notifications',
        ];

        $missing = collect($tables)
            ->reject(fn (string $table) => Schema::hasTable($table))
            ->values()
            ->all();

        return $cache = $missing;
    }

    protected function guardAgainstMissingTables(): void
    {
        $missing = $this->missingCheckoutTables();

        if ($missing !== []) {
            throw ValidationException::withMessages([
                'setup' => __('Checkout is not configured yet. Missing tables: :tables. Please run php artisan migrate.', [
                    'tables' => implode(', ', $missing),
                ]),
            ]);
        }
    }
}
