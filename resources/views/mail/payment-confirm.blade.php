@extends('mail.layouts.base')

@section('title', __('Potvrda uplate'))

@section('content')
    @php
        // Sigurno formatiranje iznosa (HR format: 1.234,56)
        $displayAmount = is_numeric($amount ?? null)
            ? number_format((float)$amount, 2, ',', '.')
            : e($amount ?? '-');

        $currency = e($payment->currency ?? '');
        $paidAt   = $payment->paid_at ? $payment->paid_at->format('d.m.Y H:i') : '—';
        $invoice  = e($payment->invoice_no ?? '—');
        $companyName = e($company->t_name ?? $company->name ?? '-');
        $subEnds = $subscription->ends_on ? $subscription->ends_on->format('d.m.Y') : '—';
    @endphp

    <h2 style="margin:0 0 12px 0; font-size:22px; line-height:1.3; font-weight:700;">
        {{ __('Potvrda o uspješnoj uplati') }}
    </h2>

    <p style="margin:0 0 12px 0;">
        {{ __('Poštovani,') }}
    </p>

    <p style="margin:0 0 16px 0;">
        {{ __('Zahvaljujemo vam na uplati! Vaša uplata za pretplatu je uspješno zaprimljena i evidentirana u sustavu.') }}
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin:25px 0;">
        <tr>
            <td style="padding:8px 0; font-weight:600; width:42%; vertical-align:top;">{{ __('Tvrtka:') }}</td>
            <td style="padding:8px 0; vertical-align:top;">{{ $companyName }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0; font-weight:600; vertical-align:top;">{{ __('Iznos uplate:') }}</td>
            <td style="padding:8px 0; vertical-align:top;">{{ $displayAmount }} @if($currency) {{ $currency }} @endif</td>
        </tr>
        <tr>
            <td style="padding:8px 0; font-weight:600; vertical-align:top;">{{ __('Valuta:') }}</td>
            <td style="padding:8px 0; vertical-align:top;">{{ $currency ?: '—' }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0; font-weight:600; vertical-align:top;">{{ __('Datum uplate:') }}</td>
            <td style="padding:8px 0; vertical-align:top;">{{ $paidAt }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0; font-weight:600; vertical-align:top;">{{ __('Broj računa:') }}</td>
            <td style="padding:8px 0; vertical-align:top; word-break:break-all;">{{ $invoice }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0; font-weight:600; vertical-align:top;">{{ __('Status:') }}</td>
            <td style="padding:8px 0; vertical-align:top;">
                <span style="background:#22c55e; color:#ffffff; border-radius:4px; padding:2px 8px; font-size:13px; display:inline-block;">
                    {{ __('Plaćeno') }}
                </span>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 16px 0;">
        {{ __('Vaša pretplata ostaje aktivna do:') }}
        <strong>{{ $subEnds }}</strong>
    </p>

    <p style="margin:0 0 25px 0;">
        {{ __('Ako imate pitanja ili trebate dodatne informacije, slobodno nas kontaktirajte.') }}
    </p>

    <p style="margin:0;">
        {{ __('Srdačan pozdrav,') }}<br>
        <strong>{{ config('app.name') }}</strong>
    </p>

    <!-- CTA -->
    <div style="text-align:center; margin-top:30px;">
        <!--[if mso]>
        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="{{ config('app.url') }}"
            style="height:44px; v-text-anchor:middle; width:260px;" arcsize="10%" fillcolor="#c92d2d" stroked="f">
            <w:anchorlock/>
            <center style="color:#ffffff; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:700;">
                {{ __('Posjetite našu stranicu') }}
        </center>
    </v:roundrect>
<![endif]-->
        <!--[if !mso]> -->
        <a href="{{ config('app.url') }}"
           style="background-color:#c92d2d; color:#ffffff; display:inline-block; padding:12px 22px; border-radius:6px;
                  text-decoration:none; font-weight:700; font-size:16px; line-height:1; min-width:220px; text-align:center;">
            {{ __('Posjetite našu stranicu') }}
        </a>
        <!--<![endif]-->
    </div>
@endsection
