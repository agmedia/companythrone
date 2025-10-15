@extends('mail.layouts.base')

@section('title', __('Potvrda uplate'))

@section('content')
    <h2 style="margin-top: 0;">{{ __('Potvrda o uspješnoj uplati') }}</h2>

    <p>
        {{ __('Poštovani,') }}
    </p>

    <p>
        {{ __('Zahvaljujemo vam na uplati! Vaša uplata za pretplatu je uspješno zaprimljena i evidentirana u sustavu.') }}
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 25px 0; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; font-weight: 600;">{{ __('Tvrtka:') }}</td>
            <td style="padding: 8px 0;">{{ $company->t_name ?? $company->name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: 600;">{{ __('Iznos uplate:') }}</td>
            <td style="padding: 8px 0;">{{ $amount }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: 600;">{{ __('Valuta:') }}</td>
            <td style="padding: 8px 0;">{{ $payment->currency }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: 600;">{{ __('Datum uplate:') }}</td>
            <td style="padding: 8px 0;">{{ $payment->paid_at?->format('d.m.Y H:i') ?? '—' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: 600;">{{ __('Broj računa:') }}</td>
            <td style="padding: 8px 0;">{{ $payment->invoice_no ?? '—' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: 600;">{{ __('Status:') }}</td>
            <td style="padding: 8px 0;">
                <span style="background:#22c55e; color:#fff; border-radius:4px; padding:2px 8px; font-size:13px;">
                    {{ __('Plaćeno') }}
                </span>
            </td>
        </tr>
    </table>

    <p>
        {{ __('Vaša pretplata ostaje aktivna do:') }}
        <strong>{{ $subscription->ends_on?->format('d.m.Y') ?? '—' }}</strong>
    </p>

    <p>
        {{ __('Ako imate pitanja ili trebate dodatne informacije, slobodno nas kontaktirajte.') }}
    </p>

    <p style="margin-top: 25px;">
        {{ __('Srdačan pozdrav,') }}<br>
        <strong>{{ config('app.name') }}</strong>
    </p>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.url') }}" class="btn">
            {{ __('Posjetite našu stranicu') }}
        </a>
    </div>
@endsection
