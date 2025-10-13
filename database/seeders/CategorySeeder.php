<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'White Goods',
                'description' => 'Large household appliances like refrigerators, washers, and dryers designed for everyday reliability.',
            ],
            [
                'name' => 'Consumer Electronics',
                'description' => 'Televisions, audio systems, personal devices, and accessories that keep customers connected.',
            ],
            [
                'name' => 'Garden Equipment',
                'description' => 'Power tools, watering systems, and maintenance essentials for year-round garden care.',
            ],
            [
                'name' => 'Outdoor Furniture',
                'description' => 'Weather-ready seating, dining sets, and shade solutions for patios, balconies, and terraces.',
            ],
            [
                'name' => 'Home Furniture',
                'description' => 'Living room, bedroom, and storage pieces curated for comfort and contemporary style.',
            ],
            [
                'name' => 'Kitchen & Dining',
                'description' => 'Cookware, tableware, and countertop helpers that streamline meal prep and hosting.',
            ],
            [
                'name' => 'Smart Home & IoT',
                'description' => 'Connected devices, sensors, and hubs that automate lighting, security, and energy management.',
            ],
            [
                'name' => 'Home Office Essentials',
                'description' => 'Ergonomic desks, seating, and productivity accessories for a focused workspace.',
            ],
            [
                'name' => 'Lighting & Fixtures',
                'description' => 'Indoor and outdoor lighting solutions, from energy-efficient bulbs to statement fixtures.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                ]
            );
        }
    }
}
