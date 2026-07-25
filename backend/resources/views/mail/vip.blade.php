@component('mail::message')
# Welcome to {{ $tier ?? 'Maison' }}

Your VELORA membership has been elevated.

Enjoy priority styling, exclusive previews, and concierge-level care reserved for our most valued members.

@component('mail::button', ['url' => config('app.frontend_url', 'http://localhost:5173').'/vip'])
Explore VIP benefits
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
