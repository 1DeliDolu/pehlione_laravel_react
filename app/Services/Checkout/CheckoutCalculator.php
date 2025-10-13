<?php

namespace App\Services\Checkout;

use App\Models\Cart;
use App\Models\DiscountCode;

class CheckoutCalculator
{
    public function calculate(Cart $cart, ?DiscountCode $discountCode, array $shippingAddress): array
    {
        $currency = $cart->currency ?? config('checkout.currency', 'EUR');
        $subtotal = $cart->items->sum(fn ($item) => (float) $item->total_price);

        $discountAmount = 0.0;
        if ($discountCode) {
            $discountAmount = min($discountCode->calculateDiscount($subtotal), $subtotal);
        }

        $netSubtotal = max(0, $subtotal - $discountAmount);
        $shippingTotal = $this->calculateShipping($netSubtotal, $shippingAddress);
        $taxTotal = 0.0; // Tax calculation can be added later when rules are defined.
        $total = $netSubtotal + $shippingTotal + $taxTotal;

        return [
            'currency' => $currency,
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountAmount, 2),
            'shipping_total' => round($shippingTotal, 2),
            'tax_total' => round($taxTotal, 2),
            'total' => round($total, 2),
        ];
    }

    protected function calculateShipping(float $netSubtotal, array $shippingAddress): float
    {
        $shippingConfig = config('checkout.shipping');
        $domesticCountry = strtoupper($shippingConfig['domestic_country'] ?? 'DE');
        $country = strtoupper($shippingAddress['country'] ?? $domesticCountry);

        $isDomestic = $country === $domesticCountry;

        if ($isDomestic && $netSubtotal >= (float) ($shippingConfig['domestic_free_threshold'] ?? 500)) {
            return 0.0;
        }

        return (float) ($isDomestic
            ? ($shippingConfig['domestic_flat_rate'] ?? 12.5)
            : ($shippingConfig['international_flat_rate'] ?? 29.9));
    }
}

