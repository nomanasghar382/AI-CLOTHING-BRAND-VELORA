@component('mail::message')
# Supplier operations update

{{ $headline ?? 'A supplier workflow requires your attention.' }}

{{ $messageLine ?? 'Review inventory synchronization, purchase orders, and fulfillment status in the supplier portal.' }}

@component('mail::button', ['url' => config('app.frontend_url', 'http://localhost:5173').'/supplier'])
Open supplier portal
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
