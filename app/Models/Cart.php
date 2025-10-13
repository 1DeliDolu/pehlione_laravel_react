<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'subtotal',
        'currency',
        'metadata',
        'submitted_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'submitted_at' => 'datetime',
        'subtotal' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function recalculateSubtotal(): void
    {
        $subtotal = $this->items->sum('total_price');
        $this->forceFill(['subtotal' => $subtotal])->save();
    }
}

