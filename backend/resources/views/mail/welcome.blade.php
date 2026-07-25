@component('mail::message')
# Welcome to VELORA

Style, intelligently yours.

Your account is ready. Explore curated modest fashion, personalized AI styling, and a global shopping experience crafted for you.

@component('mail::button', ['url' => config('app.frontend_url', 'http://localhost:5173')])
Explore VELORA
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
