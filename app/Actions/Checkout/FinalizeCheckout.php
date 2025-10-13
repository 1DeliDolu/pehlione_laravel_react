<?php

namespace App\Actions\Checkout;

use App\Mail\OrderConfirmationMail;
use App\Mail\WarehouseOrderAlertMail;
use App\Models\Address;
use App\Models\Cart;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\User;
use App\Models\WarehouseNotification;
use App\Services\Checkout\CheckoutCalculator;
use App\Services\Mail\LoggedMailSender;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FinalizeCheckout
{
    public function __construct(
        protected CheckoutCalculator $calculator,
        protected LoggedMailSender $mailSender
    ) {
    }

    /**
     * @param  array{
     *     shipping_address: array<string, mixed>,
     *     billing_address: array<string, mixed>,
     *     payment_method: string,
     *     notes?: string|null
     * }  $payload
     */
    public function handle(User $user, Cart $cart, array $payload, ?DiscountCode $discountCode = null): Order
    {
        $cart->loadMissing('items.product');

        if ($cart->items->isEmpty()) {
            throw new RuntimeException('Your cart is empty.');
        }

        $shippingData = $payload['shipping_address'] ?? [];
        $billingData = $payload['billing_address'] ?? [];

        /** @var array{address: Address, snapshot: array<string, mixed>} $shipping */
        $shipping = $this->resolveAddress($user, $shippingData, true);
        /** @var array{address: Address, snapshot: array<string, mixed>} $billing */
        $billing = $this->resolveBillingAddress($user, $billingData, $shipping);

        $totals = $this->calculator->calculate($cart, $discountCode, $shipping['snapshot']);

        /** @var Order $order */
        $order = null;
        $warehouseEmail = config('checkout.notifications.warehouse_email');

        DB::transaction(function () use (
            $user,
            $cart,
            $payload,
            $discountCode,
            $totals,
            $shipping,
            $billing,
            &$order,
            $warehouseEmail
        ) {
            $order = $user->orders()->create([
                'cart_id' => $cart->id,
                'discount_code_id' => $discountCode?->id,
                'status' => Order::STATUS_PROCESSING,
                'payment_status' => Order::PAYMENT_PAID,
                'payment_method' => $payload['payment_method'],
                'currency' => $totals['currency'],
                'subtotal' => $totals['subtotal'],
                'discount_total' => $totals['discount_total'],
                'shipping_total' => $totals['shipping_total'],
                'tax_total' => $totals['tax_total'],
                'total' => $totals['total'],
                'shipping_address' => $shipping['snapshot'],
                'billing_address' => $billing['snapshot'],
                'shipping_method' => config('checkout.shipping.default_method'),
                'notes' => $payload['notes'] ?? null,
                'placed_at' => now(),
                'paid_at' => now(),
            ]);

            $items = $cart->items->map(function ($item) use ($order) {
                return new OrderItem([
                    'product_id' => $item->product_id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'size' => $item->size !== '' ? $item->size : null,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'attribute_tags' => $item->attribute_tags ?? [],
                    'metadata' => [
                        'cart_item_id' => $item->id,
                    ],
                ]);
            });

            $order->items()->saveMany($items);

            $order->payments()->create([
                'method' => $payload['payment_method'],
                'amount' => $totals['total'],
                'currency' => $totals['currency'],
                'status' => OrderPayment::STATUS_CAPTURED,
                'reference' => 'SIM-' . strtoupper(uniqid()),
                'payload' => [
                    'simulated' => true,
                    'method' => $payload['payment_method'],
                ],
                'processed_at' => now(),
            ]);

            WarehouseNotification::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'message' => sprintf(
                    'Order #%d ready for fulfilment. Shipping to %s %s, %s %s (%s).',
                    $order->id,
                    $shipping['snapshot']['first_name'],
                    $shipping['snapshot']['last_name'],
                    $shipping['snapshot']['postal_code'],
                    $shipping['snapshot']['city'],
                    $shipping['snapshot']['country']
                ),
                'metadata' => [
                    'payment_method' => $payload['payment_method'],
                    'total' => $totals['total'],
                    'alert_email' => $warehouseEmail,
                ],
            ]);

            if ($discountCode && $totals['discount_total'] > 0) {
                $discountCode->recordRedemption($totals['discount_total'], $order);
            }

            $cart->forceFill(['status' => 'submitted'])->save();
        });

        DB::afterCommit(function () use ($user, $order, $warehouseEmail) {
            $order->loadMissing(['items', 'discountCode']);

            $this->mailSender->send(
                new OrderConfirmationMail($order),
                $user->email,
                $user->name,
                [
                    'subject' => __('Order #:number confirmation', ['number' => $order->id]),
                    'context' => [
                        'order_id' => $order->id,
                        'type' => 'customer_confirmation',
                    ],
                    'related_type' => $order::class,
                    'related_id' => $order->id,
                ],
            );

            if (! empty($warehouseEmail)) {
                $this->mailSender->send(
                    new WarehouseOrderAlertMail($order),
                    $warehouseEmail,
                    null,
                    [
                        'subject' => __('New order #:number awaiting fulfilment', ['number' => $order->id]),
                        'context' => [
                            'order_id' => $order->id,
                            'type' => 'warehouse_alert',
                        ],
                        'related_type' => $order::class,
                        'related_id' => $order->id,
                    ],
                );
            }
        });

        return $order->fresh(['items', 'discountCode']);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{address: Address, snapshot: array<string, mixed>}
     */
    protected function resolveAddress(User $user, array $data, bool $allowDefaultFlags): array
    {
        $address = null;

        if (! empty($data['id'])) {
            /** @var Address|null $address */
            $address = $user->addresses()->whereKey($data['id'])->first();

            if (! $address) {
                throw new RuntimeException('The selected address could not be found.');
            }

            if ($allowDefaultFlags) {
                $this->syncDefaults($user, $address, $data);
            }
        } else {
            $address = $this->createAddress($user, $data, $allowDefaultFlags);
        }

        return [
            'address' => $address,
            'snapshot' => $address->toSnapshot(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array{address: Address, snapshot: array<string, mixed>}  $shipping
     * @return array{address: Address, snapshot: array<string, mixed>}
     */
    protected function resolveBillingAddress(User $user, array $data, array $shipping): array
    {
        $sameAsShipping = (bool) ($data['same_as_shipping'] ?? false);

        if ($sameAsShipping) {
            $shippingAddress = $shipping['address'];

            if (! empty($data['set_as_default_billing'])) {
                $this->setDefault($user, $shippingAddress, 'is_default_billing');
            }

            return [
                'address' => $shippingAddress,
                'snapshot' => $shipping['snapshot'],
            ];
        }

        return $this->resolveAddress($user, $data, false);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createAddress(User $user, array $data, bool $allowDefaultFlags): Address
    {
        $payload = Arr::only($data, [
            'label',
            'first_name',
            'last_name',
            'company',
            'line1',
            'line2',
            'postal_code',
            'city',
            'state',
            'country',
            'phone',
        ]);

        $payload['is_default_shipping'] = $allowDefaultFlags && (bool) ($data['set_as_default_shipping'] ?? false);
        $payload['is_default_billing'] = $allowDefaultFlags && (bool) ($data['set_as_default_billing'] ?? false);

        $address = $user->addresses()->create($payload);

        if ($payload['is_default_shipping']) {
            $this->unsetOtherDefaults($user, $address, 'is_default_shipping');
        }

        if ($payload['is_default_billing']) {
            $this->unsetOtherDefaults($user, $address, 'is_default_billing');
        }

        return $address;
    }

    protected function syncDefaults(User $user, Address $address, array $data): void
    {
        $wasUpdated = false;

        if (array_key_exists('set_as_default_shipping', $data)) {
            $isDefault = (bool) $data['set_as_default_shipping'];
            $address->is_default_shipping = $isDefault;
            $wasUpdated = true;

            if ($isDefault) {
                $this->unsetOtherDefaults($user, $address, 'is_default_shipping');
            }
        }

        if (array_key_exists('set_as_default_billing', $data)) {
            $isDefault = (bool) $data['set_as_default_billing'];
            $address->is_default_billing = $isDefault;
            $wasUpdated = true;

            if ($isDefault) {
                $this->unsetOtherDefaults($user, $address, 'is_default_billing');
            }
        }

        if ($wasUpdated) {
            $address->save();
        }
    }

    protected function setDefault(User $user, Address $address, string $column): void
    {
        if (! in_array($column, ['is_default_shipping', 'is_default_billing'], true)) {
            return;
        }

        $address->{$column} = true;
        $address->save();

        $this->unsetOtherDefaults($user, $address, $column);
    }

    protected function unsetOtherDefaults(User $user, Address $address, string $column): void
    {
        $user->addresses()
            ->whereKeyNot($address->id)
            ->where($column, true)
            ->update([$column => false]);
    }
}
