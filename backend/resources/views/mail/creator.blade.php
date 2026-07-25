@component('mail::message')
# Creator program update

{{ $headline ?? 'Your creator workspace has a new update.' }}

{{ $messageLine ?? 'Review commissions, collections, and performance insights in your creator dashboard.' }}

@component('mail::button', ['url' => config('app.frontend_url', 'http://localhost:5173').'/creator/dashboard'])
Open creator dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
