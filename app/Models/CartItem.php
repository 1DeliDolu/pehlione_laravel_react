<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'name',
        'sku',
        'size',
        'quantity',
        'unit_price',
        'total_price',
        'attribute_tags',
        'metadata',
    ];

    protected $casts = [
        'attribute_tags' => 'array',
        'metadata' => 'array',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function refreshTotals(): void
    {
        $total = $this->quantity * $this->unit_price;
        $this->forceFill(['total_price' => $total])->save();
    }
}

