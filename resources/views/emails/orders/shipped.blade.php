@component('mail::message')
# {{ __('Your order is on the way!') }}

{{ __('Hello :name,', ['name' => $order->shipping_address['first_name'] ?? $order->user->name]) }}

{{ __('Great news — we have handed order #:number over to our courier.', ['number' => $order->id]) }}

@component('mail::panel')
**{{ __('Shipment summary') }}**

@foreach ($order->items as $item)
- **{{ $item->name }}** × {{ $item->quantity }}
@endforeach

**{{ __('Total') }}:** {{ number_format($order->total, 2, ',', '.') }} {{ $order->currency }}
@endcomponent

@if ($order->tracking_number)
{{ __('Tracking number') }}: **{{ $order->tracking_number }}**  
{{ __('Use the tracking number on the carrier website to follow your parcel.') }}
@endif

{{ __('Thank you for shopping with us!') }}

{{ __('Warm regards,') }}  
{{ config('app.name') }}
@endcomponent

