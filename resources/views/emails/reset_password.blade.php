@component('mail::message')
# Hello!

We received a request to reset your password. Click the button below to reset it:

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

This link will expire in 60 minutes. If you did not request a password reset, no further action is required.

Thanks,  
{{ config('app.name') }}
@endcomponent
