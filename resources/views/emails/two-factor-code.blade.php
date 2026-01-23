<x-mail::message>
    # Two Factor Authentication

    Hello,

    Your authentication code is: **{{ $code }}**

    This code will expire in 10 minutes.

    If you did not attempt to log in, please ignore this email.

    Thanks,
    {{ config('app.name') }}
</x-mail::message>