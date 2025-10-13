@component('mail::message')
# {{ __('Warehouse fulfilment update') }}

{{ __('Order #:number has a new status.', ['number' => $order->id]) }}

@component('mail::panel')
@if ($status === 'prepared')
{{ __('The package is ready for pickup. Please arrange dispatch with the carrier.') }}
@elseif ($status === 'shipped')
{{ __('The parcel has been handed to the carrier. Tracking details are listed below.') }}
@else
{{ __('A new fulfilment event was recorded for this order.') }}
@endif
@endcomponent

@if ($statusMessage)
> {{ $statusMessage }}
@endif

@component('mail::panel')
**{{ __('Order summary') }}**

@foreach ($order->items as $item)
- **{{ $item->name }}** × {{ $item->quantity }}
@endforeach

**{{ __('Customer') }}:** {{ $order->user->name }} ({{ $order->user->email }})  
**{{ __('Shipping to') }}:** {{ $order->shipping_address['first_name'] ?? '' }} {{ $order->shipping_address['last_name'] ?? '' }}, {{ $order->shipping_address['city'] ?? '' }}  
**{{ __('Total') }}:** {{ number_format($order->total, 2, ',', '.') }} {{ $order->currency }}
@endcomponent

@if ($status === 'shipped')
@if ($order->tracking_number)
{{ __('Tracking number') }}: **{{ $order->tracking_number }}**
@endif

@if ($order->delivery_estimate_at)
{{ __('Estimated delivery') }}: **{{ optional($order->delivery_estimate_at)->timezone(config('app.timezone'))->format('d.m.Y H:i') }}**
@endif
@endif

@component('mail::button', ['url' => route('mail.index')])
{{ __('View fulfilment activity') }}
@endcomponent

{{ __('Thanks for keeping our orders moving!') }}  
{{ config('app.name') }}
@endcomponent
