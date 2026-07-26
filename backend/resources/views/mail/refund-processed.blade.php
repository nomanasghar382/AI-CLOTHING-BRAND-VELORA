@component('mail::message')
# Refund processed

Your refund for order **{{ $order->order_number }}** has been processed.

**Amount:** {{ $order->currency }} {{ number_format((float) $amount, 2) }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
