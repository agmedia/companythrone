@extends('mail.layouts.base')

@section('title', __('Pozivnica od :name', ['name' => $user->name]))

@section('content')
    <h2 style="margin-top: 0;">{{ __('Pozivnica od :name', ['name' => $user->name]) }}</h2>

    <p>{{ __('Pozdrav!') }}</p>

    <p>
        {{ __(':name te poziva da se pridružiš platformi :app.', [
            'name' => $user->name,
            'app' => config('app.name')
        ]) }}
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 25px 0; border-collapse: collapse;">
        <tr>
            <td style="padding: 6px 0; font-weight: 600;">{{ __('Tvrtka pošiljatelja:') }}</td>
            <td style="padding: 6px 0;">{{ $company->t_name ?? $company->name }}</td>
        </tr>
        @if($company->weburl)
            <tr>
                <td style="padding: 6px 0; font-weight: 600;">{{ __('Web stranica:') }}</td>
                <td style="padding: 6px 0;">
                    <a href="{{ $company->weburl }}" target="_blank">{{ $company->weburl }}</a>
                </td>
            </tr>
        @endif
        @if($company->description)
            <tr>
                <td style="padding: 6px 0; font-weight: 600; vertical-align: top;">{{ __('Opis tvrtke:') }}</td>
                <td style="padding: 6px 0;">
                    {{ \Illuminate\Support\Str::limit(strip_tags($company->description), 200) }}
                </td>
            </tr>
        @endif
    </table>

    <p>
        {{ __('Klikni na gumb ispod kako bi se registrirao putem pozivnice:') }}
    </p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $referralUrl }}" class="btn">{{ __('Prihvati pozivnicu') }}</a>
    </div>

    <p style="font-size: 14px; color: #6b7280;">
        {{ __('Ako gumb ne radi, možeš otvoriti sljedeću poveznicu u pregledniku:') }}
        <br>
        <a href="{{ $referralUrl }}" target="_blank">{{ $referralUrl }}</a>
    </p>

    <p style="margin-top: 30px;">
        {{ __('Ovaj poziv je osoban i vrijedi samo za nove registracije.') }}
    </p>

    <p style="margin-top: 25px;">
        {{ __('Srdačan pozdrav,') }}<br>
        <strong>{{ config('app.name') }}</strong>
    </p>
@endsection
