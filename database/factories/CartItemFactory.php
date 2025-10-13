<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cart_id' => Cart::factory(),
            'product_id' => Product::factory(),
            'name' => fake()->words(3, true),
            'sku' => strtoupper(fake()->lexify('???')).fake()->numerify('###'),
            'size' => '',
            'quantity' => 1,
            'unit_price' => $price = fake()->randomFloat(2, 10, 200),
            'total_price' => $price,
            'attribute_tags' => [],
            'metadata' => null,
        ];
    }
}
