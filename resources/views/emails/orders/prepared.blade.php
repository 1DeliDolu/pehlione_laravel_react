@component('mail::message')
# {{ __('Your order is ready!') }}

{{ __('Hello :name,', ['name' => $order->shipping_address['first_name'] ?? $order->user->name]) }}

{{ __('We have safely prepared order #:number and it is awaiting pickup by the carrier.', ['number' => $order->id]) }}

@component('mail::panel')
**{{ __('What happens next?') }}**  
- {{ __('Our warehouse has confirmed that your items are ready for shipment.') }}  
- {{ __('We will send another notification as soon as your parcel is on its way.') }}
@endcomponent

@component('mail::panel')
**{{ __('Order summary') }}**

@foreach ($order->items as $item)
- **{{ $item->name }}** × {{ $item->quantity }}
@endforeach

**{{ __('Total') }}:** {{ number_format($order->total, 2, ',', '.') }} {{ $order->currency }}
@endcomponent

{{ __('Thank you for shopping with us!') }}

{{ __('Warm regards,') }}  
{{ config('app.name') }}
@endcomponent
