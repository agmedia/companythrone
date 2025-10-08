@component('mail::message')
    # {{ __('Pozdrav!') }}

    {{ $user->name }} te poziva da se pridružiš platformi {{ config('app.name') }}.

    @component('mail::button', ['url' => $referralUrl])
        {{ __('Registriraj se') }}
    @endcomponent

    {{ __('Ako se registriraš i dodaš svoju tvrtku, ona će započeti s višom razinom od pošiljateljeve.') }}

    {{ __('Hvala!') }}
    {{ config('app.name') }}
@endcomponent
