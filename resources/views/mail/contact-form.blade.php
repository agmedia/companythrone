@extends('mail.layouts.base')

@section('title', __('Nova poruka s kontakt forme'))

@section('content')
    <h2 style="margin-top: 0;">{{ __('Nova poruka s kontakt forme') }}</h2>

    <p>{{ __('Primili ste novu poruku s kontakt forme na web stranici.') }}</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 25px 0; border-collapse: collapse;">
        <tr>
            <td style="padding: 6px 0; font-weight: 600;">{{ __('Ime i prezime:') }}</td>
            <td style="padding: 6px 0;">{{ $name }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 0; font-weight: 600;">{{ __('E-pošta:') }}</td>
            <td style="padding: 6px 0;">
                <a href="mailto:{{ $email }}">{{ $email }}</a>
            </td>
        </tr>
        @if($subjectText)
            <tr>
                <td style="padding: 6px 0; font-weight: 600;">{{ __('Predmet poruke:') }}</td>
                <td style="padding: 6px 0;">{{ $subjectText }}</td>
            </tr>
        @endif
    </table>

    <p style="margin-top: 20px; font-weight: 600;">{{ __('Poruka:') }}</p>
    <div style="background: #f9fafb; border-radius: 6px; padding: 15px; border: 1px solid #e5e7eb;">
        {!! nl2br(e($messageText)) !!}
    </div>

    <p style="margin-top: 30px;">
        {{ __('Poruku je moguće odgovoriti izravno na:') }}
        <a href="mailto:{{ $email }}">{{ $email }}</a>
    </p>

    <p style="margin-top: 25px;">
        {{ __('Lijep pozdrav,') }}<br>
        <strong>{{ config('app.name') }}</strong>
    </p>
@endsection
