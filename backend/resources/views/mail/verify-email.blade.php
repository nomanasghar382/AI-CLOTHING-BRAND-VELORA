@component('mail::message')
# Verify your email

Please confirm your email address to secure your VELORA account.

@component('mail::button', ['url' => $url])
Verify Email
@endcomponent

If you did not create this account, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
