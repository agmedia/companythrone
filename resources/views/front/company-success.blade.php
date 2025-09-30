@extends('layouts.app')
@section('title', __('company.add'))

@section('content')
    @include('components.layouts.app..checkout-steps-nav')

    <div class="container-xxl py-4">
        <div class="row justify-content-start">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h1 class="h2 mb-3">{{ __('company.success') }}</h1>

                        {{-- Info o narudžbi / uplati (preview) --}}
                        @php
                            $locale = app()->getLocale();

                            // Naziv tvrtke za CTA / kontekst
                            $t = method_exists($company ?? null, 'translation') ? $company->translation($locale) : null;
                            $companyName = $company->t_name ?? ($t->name ?? $company->name ?? __('Tvrtka'));

                            // Order / Payment podaci (safety fallbacks)
                            $orderNumber = $order->number ?? $payment->reference ?? $payment->id ?? ('CMP-' . str_pad($company->id ?? 0, 6, '0', STR_PAD_LEFT));

                            $amount   = $payment->amount ?? $selectedPlan['price'] ?? $plan['price'] ?? null;
                            $currency = $payment->currency ?? $selectedPlan['currency'] ?? $plan['currency'] ?? 'EUR';
                            if (is_numeric($amount)) {
                                $amountPretty = rtrim(rtrim(number_format($amount, 2, ',', '.'), '0'), ',');
                            } elseif ($amount) {
                                $amountPretty = (string) $amount;
                            } else {
                                $amountPretty = '—';
                            }

                            $dt   = $order->created_at ?? $payment->created_at ?? now();
                            try { $when = $dt->format('d.m.Y. H:i'); } catch (\Throwable $e) { $when = now()->format('d.m.Y. H:i'); }

                            // Odabrani plan (može biti $selectedPlan ili $plan)
                            $planData = $selectedPlan ?? $plan ?? null;
                        @endphp

                        {{-- Sažetak narudžbe --}}
                        <div class="table-responsive mb-3">
                            <table class="table table-striped-columns align-middle">
                                <tbody>
                                <tr>
                                    <th class="w-25 text-nowrap">{{ __('Broj narudžbe:') }}</th>
                                    <td>{{ $orderNumber }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">{{ __('Iznos:') }}</th>
                                    <td>{{ $amountPretty }} {{ $amountPretty !== '—' ? $currency : '' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-nowrap">{{ __('Datum i vrijeme:') }}</th>
                                    <td>{{ $when }}</td>
                                </tr>

                                @if($planData)
                                    <tr>
                                        <th class="text-nowrap">{{ __('Odabrani paket / način plaćanja:') }}</th>
                                        <td>
                                            <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                                                <strong>{{ $planData['name'] ?? '—' }}</strong>
                                                @if(isset($planData['price']) && is_numeric($planData['price']))
                                                    <span class="text-muted">
                                                        — {{ rtrim(rtrim(number_format($planData['price'], 2, ',', '.'), '0'), ',') }}
                                                        {{ $planData['currency'] ?? 'EUR' }}
                                                    </span>
                                                @endif
                                                <a href="{{ localized_route('companies.payment') }}" class="btn btn-sm btn-outline-secondary ms-md-auto">
                                                    {{ __('common.change') ?? 'Promijeni' }}
                                                </a>
                                            </div>
                                            @if(!empty($planData['short_description']))
                                                @if (is_string($planData['short_description']))
                                                    <div class="text-muted small mt-1">{{ $planData['short_description'] }}</div>
                                                @else
                                                    <div class="text-muted small mt-1">{{ $planData['short_description'][current_locale()] }}</div>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>

                        {{-- Payment forma iz drivera --}}
                        <div class="border rounded p-3 p-md-4 bg-body-tertiary">
                            <h4 class="h5 mb-3">{{ __('Dovršetak plaćanja') }}</h4>

                            @isset($paymentView)
                                @if(View::exists($paymentView))
                                    @include($paymentView, ['data' => $paymentData ?? []])
                                @else
                                    <div class="alert alert-warning">
                                        {{ __('Nije pronađen predložak za plaćanje.') }}
                                        <div class="small text-muted">view: <code>{{ $paymentView }}</code></div>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-info">
                                    {{ __('Način plaćanja ne zahtijeva online formular ili nije konfiguriran.') }}
                                </div>
                            @endisset
                        </div>

                        {{-- Korisne akcije (fallback, ako netko želi preskočiti plaćanje) --}}
                        <div class="d-flex mt-4 gap-2">
                            <a href="{{ localized_route('companies.review') }}" class="btn btn-outline-secondary">
                                {{ __('common.back') ?? 'Natrag' }}
                            </a>
                            <a href="{{ localized_route('home') }}" class="btn btn-light">
                                {{ __('Početna') }}
                            </a>
                            @if(!empty($company))
                                <a href="{{ company_url($company) }}" class="btn btn-primary ms-auto">
                                    {{ __('Pogledaj profil:') }} {{ $companyName }}
                                </a>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{--
@extends('layouts.app')
@section('title', __('company.add'))
@section('content')

    @include('components.layouts.app..checkout-steps-nav')

    <div class="container-xxl py-4">
        <div class="row justify-content-start">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h1 class="h2 mb-3">{{ __('company.success') }}</h1>

                        <form method="post" enctype="multipart/form-data" action="{{ localized_route('companies.success') }}" class="vstack gap-3">
                            @csrf
                            <!-- Dark table with striped columns -->
                            <p>Vaša uplata je uspješno zaprimljena i vaš profil tvrtke je kreiran.</p>
                            <h4>Detalji uplate</h4>
                            <div class="table-responsive">
                                <table class="table table-striped-columns">
                                    <tbody>
                                    <tr>
                                        <th class="w-25 text-nowrap">Broj narudžbe: </th>
                                        <td>{{ $payment->reference ?? $order->number ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="w-25 text-nowrap">Iznos: </th>
                                        <td> {{ isset($payment) ? number_format($payment->amount, 2, ',', '.') : '—' }} {{ $payment->currency ?? 'EUR' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="w-25 text-nowrap">Datum i vrijeme: </th>
                                        <td>{{ isset($payment) && $payment->created_at ? $payment->created_at->format('d.m.Y. H:i') : now()->format('d.m.Y. H:i') }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <h4>Što sada?</h4>
                            <p>Nakon kratke provjere naš tim će objaviti vaš oglas/profil. O statusu ćemo vas obavijestiti e-poštom.</p> <p>Potvrdu o uplati i račun poslali smo na vašu e-mail adresu.</p>
                            <h4>Upravljanje profilom</h4>
                            <p>Svoj oglas možete urediti, dodati opis i pratiti statistike na nadzornoj ploči.</p>
                            <p>Imate pitanja? Pišite nam na info@agmedia.hr.</p>
                            <div class="d-flex mt-3 gap-2">
                                <a href="{{ route('dashboard') }}" class="btn btn-lg btn-outline-dark ">
                                    {{ __('Nadzorna ploča') }}
                                </a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

--}}
