@extends('layouts.app')

@section('title', __('Pretplate i računi'))

@section('content')
    <div class="container py-4">
        <div class="row">
            @include('front.account._sidebar')

            <div class="col-lg-9">
                <h1 class="h4 mb-4 mt-1">{{ __('Računi & Plaćanja') }}</h1>

                <div class="list-group mb-4">
                    @forelse($payments as $pay)
                        <div class="list-group-item">
                            <div class="fw-semibold">{{ $pay['name'] ?? $pay['code'] }}</div>
                            @if (is_array($pay['short_description']))
                                <div class="small text-muted">{{ $pay['short_description']['hr'] ?: '' }}</div>
                            @else
                                <div class="small text-muted">{{ $pay['short_description'] ?: '' }}</div>
                            @endif
                            @if(isset($pay['price']))
                                <div class="small">{{ __('Cijena dodatno') }}: {{ $pay['price'] }} €</div>
                            @endif

                            <div class="float-end">
                                <a href="{{ route('companies.payment') }}" class="btn btn-sm btn-outline-primary">
                                    {{ __('Plati') }}
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info mb-0">{{ __('Trenutno nema aktivnih metoda plaćanja.') }}</div>
                    @endforelse
                </div>

                <a href="{{ route('account.invoices') }}" class="btn btn-outline-primary">
                    {{ __('Pregled računa / faktura') }}
                </a>
            </div>
        </div>
    </div>
@endsection
