@component('mail::message')
# Verify Your Email Address

Dear user,

Your One-Time Password (OTP) for accessing your Flyvio account is:

@component('mail::panel')
<div style="text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px;">
{{ $otpCode }}
</div>
@endcomponent

Please use this code to complete your login or registration. The code is valid for the next 10 minutes.

If you did not request this OTP, please ignore this email.

Thanks,<br>
{{ config('app.name', 'Flyvio') }}
@endcomponent
