@component('mail::message')
# {{ __('Thank you for your order!') }}

{{ __('Hello :name,', ['name' => $order->shipping_address['first_name'] ?? $order->user->name]) }}

{{ __('We received your order #:number on :date.', ['number' => $order->id, 'date' => $order->placed_at?->format('d.m.Y H:i') ?? now()->format('d.m.Y H:i')]) }}

@component('mail::panel')
**{{ __('Order summary') }}**

@foreach ($order->items as $item)
- **{{ $item->name }}** × {{ $item->quantity }} — {{ number_format($item->total_price, 2, ',', '.') }} {{ $order->currency }}
@endforeach

**{{ __('Subtotal') }}:** {{ number_format($order->subtotal, 2, ',', '.') }} {{ $order->currency }}  
@if ($order->discount_total > 0)
**{{ __('Discounts') }}:** −{{ number_format($order->discount_total, 2, ',', '.') }} {{ $order->currency }}  
@endif
**{{ __('Shipping') }}:** {{ number_format($order->shipping_total, 2, ',', '.') }} {{ $order->currency }}  
**{{ __('Total') }}:** {{ number_format($order->total, 2, ',', '.') }} {{ $order->currency }}
@endcomponent

**{{ __('Shipping address') }}**  
{{ $order->shipping_address['first_name'] ?? '' }} {{ $order->shipping_address['last_name'] ?? '' }}  
@if (!empty($order->shipping_address['company']))
{{ $order->shipping_address['company'] }}  
@endif
{{ $order->shipping_address['line1'] ?? '' }}  
@if (!empty($order->shipping_address['line2']))
{{ $order->shipping_address['line2'] }}  
@endif
{{ $order->shipping_address['postal_code'] ?? '' }} {{ $order->shipping_address['city'] ?? '' }}  
{{ $order->shipping_address['country'] ?? '' }}

@component('mail::table')
| {{ __('Payment method') }} | {{ __('Status') }} |
| :------------------------ | :----------------- |
| {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }} | {{ ucfirst($order->payment_status) }} |
@endcomponent

{{ __('You will receive another email once your package ships. If you have questions, reply to this email and the team will help you shortly.') }}

{{ __('Warm regards,') }}  
{{ config('app.name') }}
@endcomponent

