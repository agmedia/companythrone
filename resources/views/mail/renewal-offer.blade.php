@extends('mail.layouts.base')

@section('content')
    <p style="margin:0 0 16px 0;">
        {{ __('mail.greeting', ['company' => e($company->name ?? '-')]) }}
    </p>

    <p style="margin:0 0 16px 0;">
        {{ __('mail.renewal_offer_body', ['amount' => e($amount ?? '-')]) }}
    </p>

    <p style="margin:0;">
        {{ __('mail.signoff') }}<br>
        <strong>{{ __('mail.signature') }}</strong>
    </p>
@endsection
