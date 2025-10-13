<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        $sizeProfiles = [
            'standard_apparel' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
            'general' => ['Small', 'Medium', 'Large'],
            'one_size' => ['One Size'],
        ];

        $sizeProfile = fake()->randomElement(array_keys($sizeProfiles));
        $availableSizes = $sizeProfiles[$sizeProfile];
        $stockQuantity = fake()->numberBetween(15, 200);

        $attributePool = [
            'eco-friendly',
            'recyclable',
            'modular',
            'smart-enabled',
            'chemical-free',
            'energy-efficient',
            'water-resistant',
        ];

        $carePool = [
            'Wipe with damp cloth',
            'Machine wash cold',
            'Air dry only',
            'Store in dry area',
            'Avoid direct sunlight',
        ];

        $sustainabilityPool = [
            'Made with recycled materials',
            'Certified low-emission manufacturing',
            'Designed for easy disassembly and recycling',
            'Natural, chemical-free finish',
        ];

        $attributeTags = fake()->randomElements($attributePool, fake()->numberBetween(2, 4));
        $careInstructions = fake()->randomElements($carePool, fake()->numberBetween(2, 3));
        $sustainabilityNotes = fake()->randomElements($sustainabilityPool, fake()->numberBetween(2, 3));
        $stockStatus = $stockQuantity < 25 ? 'low_stock' : 'in_stock';

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'sku' => strtoupper(Str::random(3)).fake()->unique()->numerify('###'),
            'summary' => fake()->sentence(12),
            'description' => fake()->paragraphs(2, true),
            'size_profile' => $sizeProfile,
            'available_sizes' => $availableSizes,
            'material_profile' => fake()->randomElement(['natural', 'synthetic', 'hybrid', 'recycled']),
            'attribute_tags' => $attributeTags,
            'sustainability_notes' => $sustainabilityNotes,
            'care_instructions' => $careInstructions,
            'price' => fake()->randomFloat(2, 49, 1899),
            'currency' => 'EUR',
            'stock_status' => $stockStatus,
            'stock_quantity' => $stockQuantity,
            'is_active' => true,
            'lead_time_days' => fake()->randomElement([null, 3, 5, 7, 14]),
            'energy_label' => fake()->randomElement([null, 'A++', 'A+', 'A', 'B']),
            'images' => [],
            'metadata' => [
                'origin_country' => fake()->countryCode(),
                'warranty_months' => fake()->randomElement([12, 24, 36]),
                'lifecycle_stage' => fake()->randomElement(['standard', 'seasonal', 'limited'] ),
            ],
        ];
    }
}
