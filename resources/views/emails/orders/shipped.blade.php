@component('mail::message')
# {{ __('Your order is on the way!') }}

{{ __('Hello :name,', ['name' => $order->shipping_address['first_name'] ?? $order->user->name]) }}

{{ __('Great news — we have approved order #:number and handed it over to our courier.', ['number' => $order->id]) }}

{{ __('The package is now with the carrier and moving through their delivery network.') }}

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

@if ($order->delivery_estimate_at)
{{ __('Estimated delivery') }}: **{{ optional($order->delivery_estimate_at)->timezone(config('app.timezone'))->format('d.m.Y H:i') }}**

{{ __('We will let you know if the carrier updates this delivery window.') }}
@endif

{{ __('Thank you for shopping with us!') }}

{{ __('Warm regards,') }}  
{{ config('app.name') }}
@endcomponent
