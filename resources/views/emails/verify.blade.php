<x-mail::message>
# Verify your email address

Hi {{ $details['username'] }}, you just created an account on {{ config('app.name') }}, in order to continue please verify your email address

<a href="{{ $details['url'] }}">{{ $details['url'] }}</a>

Ignore the mail, if you didnt make this request

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
