@component('mail::message')
# Your order has shipped

**Order:** {{ $order->order_number }}  
**Tracking:** {{ $trackingNumber ?? 'Available in your account' }}

@component('mail::button', ['url' => config('app.frontend_url', 'http://localhost:5173').'/orders/'.$order->id])
Track Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
