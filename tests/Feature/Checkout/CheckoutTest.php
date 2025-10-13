<?php

use App\Enums\Role;
use App\Mail\OrderConfirmationMail;
use App\Mail\WarehouseOrderAlertMail;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\User;
use App\Models\WarehouseNotification;
use App\Models\MailLog;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('creates an order from the active cart with simulated payment and notifications', function () {
    Mail::fake();

    /** @var User $user */
    $user = User::factory()->create([
        'role' => Role::KUNDEN,
    ]);

    /** @var Cart $cart */
    $cart = Cart::factory()->for($user)->create([
        'status' => 'active',
        'currency' => 'EUR',
    ]);

    /** @var CartItem $item */
    $item = CartItem::factory()
        ->for($cart)
        ->create([
            'quantity' => 2,
            'unit_price' => 300,
            'total_price' => 600,
        ]);

    $cart->load('items');
    $cart->recalculateSubtotal();

    $discount = DiscountCode::create([
        'code' => 'SOMMER10',
        'type' => 'percentage',
        'amount' => 10,
        'currency' => 'EUR',
        'is_active' => true,
        'usage_limit' => null,
        'usage_count' => 0,
        'min_subtotal' => 100,
    ]);

    $payload = [
        'shipping_address' => [
            'label' => 'Home',
            'first_name' => 'Anna',
            'last_name' => 'Müller',
            'company' => '',
            'line1' => 'Teststraße 1',
            'line2' => '',
            'postal_code' => '10115',
            'city' => 'Berlin',
            'state' => '',
            'country' => 'DE',
            'phone' => '+49 30 123456',
            'set_as_default_shipping' => true,
            'set_as_default_billing' => false,
        ],
        'billing_address' => [
            'same_as_shipping' => true,
            'set_as_default_billing' => true,
        ],
        'payment_method' => 'paypal',
        'discount_code' => $discount->code,
    ];

    $response = actingAs($user)
        ->post('/checkout', $payload);

    $order = Order::with(['items', 'payments', 'discountCode'])->first();
    expect($order)->not->toBeNull();

    $response->assertRedirect(route('checkout.confirmation', $order));

    expect((float) $order->subtotal)->toBe(600.0)
        ->and((float) $order->discount_total)->toBe(60.0)
        ->and((float) $order->shipping_total)->toBe(0.0)
        ->and((float) $order->total)->toBe(540.0)
        ->and($order->payment_status)->toBe('paid')
        ->and($order->payment_method)->toBe('paypal')
        ->and($order->items)->toHaveCount(1);

    expect(WarehouseNotification::count())->toBe(1);
    $notification = WarehouseNotification::first();
    expect($notification->metadata['alert_email'] ?? null)->toBe(config('checkout.notifications.warehouse_email'));

    expect(MailLog::count())->toBe(2);
    expect(OrderPayment::count())->toBe(1);

    $cart->refresh();
    expect($cart->status)->toBe('submitted');

    Mail::assertSent(OrderConfirmationMail::class, function (OrderConfirmationMail $mail) use ($order) {
        return $mail->order->is($order);
    });

    Mail::assertSent(WarehouseOrderAlertMail::class, function (WarehouseOrderAlertMail $mail) use ($order) {
        return $mail->order->is($order);
    });
});
