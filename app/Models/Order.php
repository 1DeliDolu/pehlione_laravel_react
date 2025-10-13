<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PROCESSING = 'processing';
    public const STATUS_READY_TO_SHIP = 'ready_to_ship';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'cart_id',
        'discount_code_id',
        'status',
        'payment_status',
        'payment_method',
        'currency',
        'subtotal',
        'discount_total',
        'shipping_total',
        'tax_total',
        'total',
        'shipping_address',
        'billing_address',
        'shipping_method',
        'notes',
        'placed_at',
        'paid_at',
        'prepared_at',
        'shipped_at',
        'delivery_estimate_at',
        'tracking_number',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'shipping_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'placed_at' => 'datetime',
        'paid_at' => 'datetime',
        'prepared_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivery_estimate_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function discountCode(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(OrderPayment::class)->latestOfMany();
    }

    public function warehouseNotifications(): HasMany
    {
        return $this->hasMany(WarehouseNotification::class);
    }

    public function isDomestic(): bool
    {
        $domesticCountry = config('checkout.shipping.domestic_country');
        $shippingCountry = $this->shipping_address['country'] ?? null;

        return $shippingCountry !== null && strtoupper($shippingCountry) === strtoupper($domesticCountry);
    }
}
