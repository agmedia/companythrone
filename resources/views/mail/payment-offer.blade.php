@extends('mail.layouts.base')

@section('content')
    <p>{{ __('mail.greeting', ['company' => $company->name]) }}</p>

    <p>{{ __('mail.payment_offer_body', ['amount' => $amount]) }}</p>

    <p>
        {{ __('mail.signoff') }}<br>
        {{ __('mail.signature') }}
    </p>
@endsection
