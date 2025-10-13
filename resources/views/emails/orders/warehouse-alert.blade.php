@component('mail::message')
# {{ __('New order requires fulfilment') }}

{{ __('Order #:number has been placed and needs attention from the warehouse team.', ['number' => $order->id]) }}

@component('mail::panel')
- {{ __('Open the fulfilment dashboard to review the order details.') }}
- {{ __('Confirm the package once it is prepared.') }}
- {{ __('Mark the parcel as dispatched after handing it to the carrier.') }}
@endcomponent

@component('mail::button', ['url' => route('mail.index')])
{{ __('Review fulfilment queue') }}
@endcomponent

@component('mail::panel')
**{{ __('Customer') }}:** {{ $order->user->name }} ({{ $order->user->email }})  
**{{ __('Placed at') }}:** {{ optional($order->placed_at)->format('d.m.Y H:i') ?? now()->format('d.m.Y H:i') }}  
**{{ __('Total') }}:** {{ number_format($order->total, 2, ',', '.') }} {{ $order->currency }}
@endcomponent

@component('mail::table')
| {{ __('Item') }} | {{ __('Qty') }} | {{ __('Total') }} |
| :--------------- | :-------------: | -----------------:|
@foreach ($order->items as $item)
| {{ $item->name }} | {{ $item->quantity }} | {{ number_format($item->total_price, 2, ',', '.') }} {{ $order->currency }} |
@endforeach
@endcomponent

**{{ __('Shipping to') }}**  
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

{{ __('Please follow the steps above to keep the customer updated throughout fulfilment.') }}

{{ __('Thanks,') }}  
{{ config('app.name') }}
@endcomponent
