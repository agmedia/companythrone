@extends('layouts.app')
@section('title', __('Moj račun'))
@section('content')
    <div class="container py-4">
        <h1 class="h3 mb-4">{{ __('Moj račun') }}</h1>

        <div class="row g-3">
            @include('front.account._sidebar')


            <div class="col-lg-9">

            <div class="row g-3">

                {{-- Pretplate i plaćanja
                <div class="col-md-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="text-muted small">{{ __('Pretplate i plaćanja') }}</div>

                            @if($user->company && $user->company->subscriptions()->active()->exists())
                                @php
                                    $sub = $user->company->subscriptions()->active()->latest('starts_on')->first();
                                    $lastPayment = $user->company->payments()->paid()->latest('paid_at')->first();
                                @endphp

                                <div class="fw-semibold mt-2">
                                    {{ ucfirst($sub->plan) }} ({{ $sub->period }})
                                </div>
                                <div class="small text-muted">
                                    {{ __('Do') }} {{ optional($sub->ends_on)->format('d.m.Y.') ?? '—' }}
                                </div>

                                @if($lastPayment)
                                    <div class="small text-success">
                                        {{ __('Zadnja uplata:') }}
                                        {{ number_format($lastPayment->amount, 2, ',', '.') }} {{ $lastPayment->currency }}
                                    </div>
                                @endif
                            @else
                                <div class="small text-muted mt-2">{{ __('Nema aktivne pretplate') }}</div>
                            @endif

                            <a class="btn btn-sm btn-outline-primary mt-auto" href="{{ localized_route('account.subscriptions') }}">
                                {{ __('Pregledaj') }}
                            </a>
                        </div>
                    </div>
                </div>
--}}
                {{-- Profil tvrtke --}}
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="text-muted small">{{ __('Profil') }}</div>

                            @if($user->company)
                                <div class="fw-semibold mt-2">
                                    {{ $user->company->translation()?->name ?? $user->company->oib }}
                                </div>
                                <div class="small text-muted">{{ $user->company->email }}</div>
                            @else
                                <div class="small text-muted mt-2">{{ __('Nema podataka o tvrtki') }}</div>
                            @endif

                            <a class="btn btn-sm btn-outline-primary mt-3" href="{{ localized_route('account.profile.edit') }}">
                                {{ __('Uredi profil') }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Moji linkovi (dnevni zadaci) --}}
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="text-muted small">{{ __('Moji linkovi') }}</div>

                            <div class="fw-semibold mt-2">
                                {{ $todayClicks }} / {{ $limitPerDay }}
                            </div>
                            <div class="small text-muted">
                                {{ __('Dnevni zadatak') }}
                            </div>

                            <a class="btn btn-sm btn-outline-primary mt-3" href="{{ localized_route('account.links.index') }}">
                                {{ __('Pregledaj') }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Referral preporuke --}}
                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="text-muted small">{{ __('Preporuke') }}</div>

                            @php
                                $refCount = \App\Models\Shared\ReferralLink::where('user_id', $user->id)->count();
                            @endphp

                            <div class="fw-semibold mt-2">
                                {{ $refCount }} / 5
                            </div>
                            <div class="small text-muted">
                                {{ __('Potrebno za aktivaciju') }}
                            </div>

                            <a class="btn btn-sm btn-outline-primary mt-3" href="{{ localized_route('account.links.index') }}">
                                {{ __('Dodaj preporuku') }}
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            </div>


        </div>
    </div>
@endsection
