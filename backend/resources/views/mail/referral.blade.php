@component('mail::message')
# Your referral invitation

{{ $referrerName ?? 'A VELORA member' }} invited you to join the Circle.

Use your personal invitation to unlock welcome rewards when you create your account.

@component('mail::button', ['url' => $inviteUrl ?? config('app.frontend_url', 'http://localhost:5173').'/register'])
Accept invitation
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
