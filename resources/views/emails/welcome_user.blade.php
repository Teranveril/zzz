@component('mail::message')

    Witamy użytkownika {{ $user->first_name }} {{ $user->last_name }}

@endcomponent
