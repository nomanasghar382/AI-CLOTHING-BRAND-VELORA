@component('mail::message')
# Reset your password

We received a request to reset your VELORA password.

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

This link expires shortly. If you did not request a reset, you can ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
