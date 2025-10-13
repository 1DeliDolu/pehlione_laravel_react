<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'summary',
        'description',
        'size_profile',
        'available_sizes',
        'material_profile',
        'attribute_tags',
        'sustainability_notes',
        'care_instructions',
        'images',
        'price',
        'currency',
        'stock_status',
        'stock_quantity',
        'is_active',
        'lead_time_days',
        'energy_label',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'available_sizes' => 'array',
            'attribute_tags' => 'array',
            'sustainability_notes' => 'array',
            'care_instructions' => 'array',
            'images' => 'array',
            'metadata' => 'array',
            'is_active' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
