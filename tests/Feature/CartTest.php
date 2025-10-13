<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;

it('allows an authenticated user to add products to the cart', function (): void {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'slug' => 'test-product',
        'sku' => 'TST-001',
        'price' => 199,
        'available_sizes' => ['M'],
    ]);

    $this->actingAs($user)
        ->post('/cart/items', [
            'product_slug' => $product->slug,
            'quantity' => 2,
            'size' => 'M',
        ])
        ->assertRedirect();

    $cart = Cart::where('user_id', $user->id)->where('status', 'active')->first();

    expect($cart)->not()->toBeNull();
    expect($cart->items)->toHaveCount(1);
    expect($cart->subtotal)->toEqual(398.0);
});

it('updates existing cart items instead of duplicating rows', function (): void {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Second Product',
        'slug' => 'second-product',
        'sku' => 'SEC-001',
        'price' => 99,
        'available_sizes' => ['One Size'],
    ]);

    $this->actingAs($user)
        ->post('/cart/items', [
            'product_slug' => $product->slug,
            'quantity' => 1,
            'size' => 'One Size',
        ])->assertRedirect();

    $this->actingAs($user)
        ->post('/cart/items', [
            'product_slug' => $product->slug,
            'quantity' => 3,
            'size' => 'One Size',
        ])->assertRedirect();

    $cart = Cart::where('user_id', $user->id)->where('status', 'active')->with('items')->first();

    expect($cart?->items)->toHaveCount(1);
    expect($cart?->items->first()->quantity)->toEqual(4);
    expect($cart?->subtotal)->toEqual(396.0);
});

it('prevents users from modifying carts that are not theirs', function (): void {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Protected Product',
        'slug' => 'protected-product',
        'sku' => 'PRO-001',
        'price' => 59,
    ]);

    $cart = Cart::factory()->for($owner)->create();
    $item = CartItem::factory()->for($cart)->for($product)->create([
        'name' => $product->name,
        'sku' => $product->sku,
        'quantity' => 1,
        'unit_price' => $product->price,
        'total_price' => $product->price,
    ]);

    $this->actingAs($other)
        ->patch(route('cart.items.update', $item), ['quantity' => 2])
        ->assertForbidden();
});
