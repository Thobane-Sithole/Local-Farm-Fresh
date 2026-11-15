<x-mail::message>
# {{ $user->name }},

@if ($otp->type === 'login')
Someone (hopefully you) is trying to log in to your Local-Farm-Fresh account.
@else
You requested a password reset for your Local-Farm-Fresh account.
@endif

Your verification code is:

<x-mail::panel>
# {{ $otp->code }}
</x-mail::panel>

This code expires in **10 minutes**. Never share it with anyone.

If you did not request this, you can safely ignore this email — your account has not been accessed.

Thanks,<br>
The Local-Farm-Fresh Team
</x-mail::message>
