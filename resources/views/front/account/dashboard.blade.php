@extends('layouts.app')
@section('title', __('Moj račun'))
@section('content')
    <div class="container py-4">
        <h1 class="h3 mb-4">{{ __('Moj račun') }}</h1>

        <div class="row g-3">
            @include('front.account._sidebar')
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('Današnji linkovi') }}</div>
                        <div class="display-6">{{ $todayLinks }} / {{ $limitPerDay }}</div>
                        <a class="btn btn-sm btn-outline-primary mt-2" href="{{ localized_route('account.links.index') }}">{{ __('Upravljaj linkovima') }}</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('Pretplate i plaćanja') }}</div>
                        <a class="btn btn-sm btn-outline-primary mt-2" href="{{ localized_route('account.subscriptions') }}">{{ __('Pregledaj') }}</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small">{{ __('Profil') }}</div>
                        <a class="btn btn-sm btn-outline-primary mt-2" href="{{ localized_route('account.profile.edit') }}">{{ __('Uredi profil') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
