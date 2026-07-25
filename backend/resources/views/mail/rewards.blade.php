@component('mail::message')
# Rewards unlocked

You have new rewards waiting in your VELORA Circle membership.

{{ $messageLine ?? 'Redeem points for styling sessions, early access, and store credit.' }}

@component('mail::button', ['url' => config('app.frontend_url', 'http://localhost:5173').'/rewards'])
View rewards
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
