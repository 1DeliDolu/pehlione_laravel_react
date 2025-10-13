<?php

use App\Models\Category;
use Inertia\Testing\AssertableInertia as Assert;

it('renders categories from the database on the products page', function (): void {
    Category::factory()->create([
        'name' => 'Bravo',
        'slug' => 'bravo',
    ]);

    Category::factory()->create([
        'name' => 'Alpha',
        'slug' => 'alpha',
    ]);

    Category::factory()->create([
        'name' => 'Charlie',
        'slug' => 'charlie',
    ]);

    $this->get('/products')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('products')
            ->has('categories', 3)
            ->where('categories.0.name', 'Alpha')
            ->where('categories.1.name', 'Bravo')
            ->where('categories.2.name', 'Charlie')
            ->where('categories.0.slug', 'alpha')
            ->where('categories.1.slug', 'bravo')
            ->where('categories.2.slug', 'charlie')
        );
});
