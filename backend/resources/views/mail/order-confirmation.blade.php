@component('mail::message')
# Order confirmed

Thank you for your purchase.

**Order:** {{ $order->order_number }}  
**Total:** {{ $order->currency }} {{ number_format((float) $order->total_amount, 2) }}

@component('mail::button', ['url' => config('app.frontend_url', 'http://localhost:5173').'/orders/'.$order->id])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
