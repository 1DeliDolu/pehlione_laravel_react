<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'type',
        'amount',
        'currency',
        'usage_limit',
        'usage_count',
        'min_subtotal',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'min_subtotal' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function redemptions(): HasMany
    {
        return $this->hasMany(DiscountRedemption::class);
    }

    public function isCurrentlyActive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = CarbonImmutable::now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function canApplyToSubtotal(float $subtotal): bool
    {
        return $subtotal >= (float) $this->min_subtotal;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if (! $this->isCurrentlyActive() || ! $this->canApplyToSubtotal($subtotal)) {
            return 0.0;
        }

        if ($this->type === 'percentage') {
            $amount = $subtotal * ($this->amount / 100);
            return round($amount, 2);
        }

        return (float) $this->amount;
    }

    public function recordRedemption(float $amount, Order $order): void
    {
        $this->redemptions()->create([
            'order_id' => $order->id,
            'amount' => $amount,
        ]);

        $this->increment('usage_count');
    }
}

