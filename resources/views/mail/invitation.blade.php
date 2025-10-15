@extends('mail.layouts.base')

@section('content')
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            {{ __('Pozdrav!') }}

            {{ $user->name }} te poziva da se pridružiš platformi {{ config('app.name') }}.
        </tr>
        <tr>
            <a href="{{ route('register') }}">{{ __('Registriraj se') }}</a>
        </tr>
        <tr>
            {{ __('Ako se registriraš i dodaš svoju tvrtku, ona će započeti s višom razinom od pošiljateljeve.') }}

            {{ __('Hvala!') }}
            {{ config('app.name') }}
        </tr>
    </table>
@endsection