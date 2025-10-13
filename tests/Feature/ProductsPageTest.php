<?php

use App\Models\Category;
use App\Models\Product;
use Inertia\Testing\AssertableInertia as Assert;

it('renders categories with products from the catalogue', function (): void {
    $electronics = Category::factory()->create([
        'name' => 'Consumer Electronics',
        'slug' => 'consumer-electronics',
    ]);

    $lighting = Category::factory()->create([
        'name' => 'Lighting & Fixtures',
        'slug' => 'lighting-and-fixtures',
    ]);

    Product::factory()->for($electronics)->create([
        'name' => 'Test Display Panel',
        'slug' => 'test-display-panel',
        'sku' => 'ELC-001',
        'price' => 499,
        'attribute_tags' => ['energy-efficient'],
        'available_sizes' => ['42"'],
        'sustainability_notes' => ['Sample sustainability note'],
        'stock_quantity' => 27,
        'images' => ['/products/placeholders/placeholder-1.png', '/products/placeholders/placeholder-2.png'],
    ]);

    Product::factory()->for($lighting)->create([
        'name' => 'Test Ambient Lamp',
        'slug' => 'test-ambient-lamp',
        'sku' => 'LGT-001',
        'price' => 129,
        'attribute_tags' => ['eco-friendly'],
        'available_sizes' => ['One Size'],
        'sustainability_notes' => ['Recyclable housing'],
        'stock_quantity' => 14,
        'images' => ['/products/placeholders/placeholder-1.png'],
    ]);

    $this->get('/products')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('products')
            ->has('categories', 2)
            ->where('categories.0.name', 'Consumer Electronics')
            ->where('categories.0.products.0.name', 'Test Display Panel')
            ->where('categories.0.products.0.sku', 'ELC-001')
            ->where('categories.0.products.0.price', 499)
            ->where('categories.1.name', 'Lighting & Fixtures')
            ->where('categories.1.products.0.name', 'Test Ambient Lamp')
            ->where('categories.1.products.0.attribute_tags.0', 'eco-friendly')
        );
});

it('shows product details including size options and images', function (): void {
    $category = Category::factory()->create([
        'name' => 'Outdoor Furniture',
        'slug' => 'outdoor-furniture',
    ]);

    $product = Product::factory()->for($category)->create([
        'name' => 'Eco Patio Lounger',
        'slug' => 'eco-patio-lounger',
        'sku' => 'OUT-999',
        'price' => 799,
        'available_sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
        'attribute_tags' => ['eco-friendly', 'modular'],
        'sustainability_notes' => ['Recycled aluminium frame'],
        'care_instructions' => ['Cover during heavy rain'],
        'stock_quantity' => 8,
        'stock_status' => 'low_stock',
        'lead_time_days' => 10,
        'images' => [
            '/products/placeholders/placeholder-1.png',
            '/products/placeholders/placeholder-2.png',
            '/products/placeholders/placeholder-3.png',
        ],
    ]);

    $this->get("/products/{$product->slug}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('products/show')
            ->where('product.name', 'Eco Patio Lounger')
            ->where('product.sku', 'OUT-999')
            ->where('product.available_sizes.2', 'L')
            ->where('product.images.0', '/products/placeholders/placeholder-1.png')
            ->where('product.stock_status', 'low_stock')
            ->where('product.category.name', 'Outdoor Furniture')
        );
});
