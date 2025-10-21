@extends('layouts.app')

@section('title', __('Moj račun'))

@section('content')
    <div class="container py-4">
        <div class="row g-3">
            @include('front.account._sidebar')
            <div class="col-lg-9">
                <h1 class="h4 mb-4 mt-1">{{ __('back/nav.dashboard') }}</h1>

                <div class="row g-3">
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

                    @if (auth()->user()->hasRole('company_owner') && $user->company && subscription_active($user->company->id))
                        {{-- Moji linkovi (dnevni zadaci) --}}
                        <div class="col-md-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body d-flex flex-column">
                                    <div class="text-muted small">{{ __('Moji linkovi') }}</div>

                                    <div class="fw-semibold mt-2">
                                        {{ $todayClicks }} / {{ $limitPerDay }}
                                    </div>

                                    @if ($limitPerDay > $todayClicks)
                                        <div class="small text-muted">
                                            {{ __('Dnevni zadatak') }}
                                        </div>

                                        <a class="btn btn-sm btn-outline-primary mt-3" href="{{ localized_route('account.links.index') }}">
                                            {{ __('Pregledaj') }}
                                        </a>
                                    @else
                                        <span class="badge bg-success mt-1">{{ __('Aktivirano') }}</span>
                                        <a class="btn btn-sm btn-outline-success mt-2" href="{{ localized_route('account.links.index') }}">
                                            {{ __('Pogledaj') }}
                                        </a>
                                    @endif


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
                                        {{ $refCount }} / {{ $ref_limit }}
                                    </div>

                                    @if ($ref_limit > $refCount)
                                        <div class="small text-muted">
                                            {{ __('Potrebno za aktivaciju') }}
                                        </div>
                                        <a class="btn btn-sm btn-outline-primary mt-3" href="{{ localized_route('account.links.index') }}">
                                            {{ __('Dodaj preporuku') }}
                                        </a>
                                    @else
                                        <span class="badge bg-success mt-1">{{ __('Aktivirano') }}</span>
                                        <a class="btn btn-sm btn-outline-success mt-2" href="{{ localized_route('account.links.index') }}">
                                            {{ __('Pogledaj') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>


        </div>
    </div>
@endsection
