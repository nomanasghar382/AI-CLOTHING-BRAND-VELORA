@component('mail::message')
# The VELORA edit

{{ $headline ?? 'This week in modest fashion.' }}

{{ $messageLine ?? 'Discover curated edits, creator looks, and seasonal styling inspiration from the VELORA team.' }}

@component('mail::button', ['url' => config('app.frontend_url', 'http://localhost:5173').'/catalog'])
Shop the edit
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
