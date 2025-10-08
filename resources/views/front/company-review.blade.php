
@extends('layouts.app')
@section('title', __('company.add'))
{{--
@section('content')
    @include('components.layouts.app..checkout-steps-nav')

    <div class="container-xxl py-4">
        <div class="row justify-content-start">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h1 class="h2 mb-3">{{ __('company.review') }}</h1>

                        <form method="post" enctype="multipart/form-data" action="{{ localized_route('companies.success') }}" class="vstack gap-3">
                            @csrf

                            @php
                                $locale = app()->getLocale();
                                // name / description iz prijevoda ili osnovnih polja
                                $t = method_exists($company, 'translation') ? $company->translation($locale) : null;
                                $name = $company->t_name ?? ($t->name ?? ($company->name ?? '—'));
                                $desc = $company->t_desc ?? ($t->description ?? $t->text ?? $t->body ?? ($company->description ?? null));

                                // adresa
                                $streetLine = trim(($company->street ?? '').' '.($company->street_no ?? ''));
                                $cityState  = trim(($company->city ?? '').($company->city && $company->state ? ', ' : '').($company->state ?? ''));
                                $address    = trim($streetLine.' '.$cityState);

                                // kategorije (ako relacija postoji)
                                $cats = collect();
                                if (method_exists($company, 'categories')) {
                                    $cats = $company->categories()->with('translations')->get()
                                        ->map(function($c) use ($locale) {
                                            $ct = method_exists($c, 'translation') ? $c->translation($locale) : null;
                                            return $ct->name ?? $c->name ?? null;
                                        })->filter()->values();
                                }
                            @endphp

                                    <!-- Pregled podataka -->
                            <div class="table-responsive">
                                <table class="table table-striped-columns align-middle">
                                    <tbody>
                                    <tr>
                                        <th class="w-25 text-nowrap">Naziv tvrtke::</th>
                                        <td>{{ $name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap">OIB:</th>
                                        <td>{{ $company->oib ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap">E-mail:</th>
                                        <td>
                                            @if(!empty($company->email))
                                                <a href="mailto:{{ $company->email }}">{{ $company->email }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap">Telefon:</th>
                                        <td>
                                            @if(!empty($company->phone))
                                                <a href="tel:{{ $company->phone }}">{{ $company->phone }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap">Web stranica:</th>
                                        <td>
                                            @if(!empty($company->weburl))
                                                <a href="{{ $company->weburl }}" target="_blank" rel="noopener">{{ $company->weburl }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-nowrap">Adresa:</th>
                                        <td>{{ $address !== '' ? $address : '—' }}</td>
                                    </tr>

                                    @if($cats->isNotEmpty())
                                        <tr>
                                            <th class="text-nowrap">Kategorije:</th>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($cats as $catName)
                                                        <span class="badge text-bg-light">{{ $catName }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <th class="text-nowrap">Kratki opis:</th>
                                        <td>
                                            @if(!empty($desc))
                                                {{ $desc }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>

                                    @isset($selectedPlan)
                                        <tr>
                                            <th class="text-nowrap">Način plaćanja / paket:</th>
                                            <td class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                                                <div>
                                                    <strong>{{ $selectedPlan['name'] ?? '—' }}</strong>
                                                    @if(isset($selectedPlan['price']))
                                                        <span class="ms-2">
                                                            @php
                                                                $price = $selectedPlan['price'];
                                                                $cur = $selectedPlan['currency'] ?? 'EUR';
                                                                $pretty = is_numeric($price)
                                                                    ? rtrim(rtrim(number_format($price, 2, '.', ''), '0'), '.')
                                                                    : $price;
                                                            @endphp
                                                            — {{ $pretty }} {{ $cur }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <a href="{{ localized_route('companies.payment') }}" class="btn btn-sm btn-outline-secondary ms-md-auto">
                                                    Promijeni
                                                </a>

                                                @if(!empty($selectedPlan['short_description']))
                                                    @if (is_string($selectedPlan['short_description']))
                                                        <div class="text-muted small w-100">{{ $selectedPlan['short_description'] }}</div>
                                                    @else
                                                        <div class="text-muted small w-100">{{ $selectedPlan['short_description'][current_locale()] }}</div>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endisset
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex mt-3 gap-2">
                                <a href="{{ localized_route('companies.create') }}" class="btn btn-lg btn-outline-dark ms-0">
                                    <i class="fi-chevron-left fs-lg me-1 ms-n2"></i> {{ __('company.back') }}
                                </a>
                                <button type="submit" class="btn btn-lg btn-primary ms-auto">
                                    {{ __('company.submit') }} <i class="fi-chevron-right fs-lg ms-1 me-n2"></i>
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

--}}
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
                            $orderNumber = $subscription->number . '-' . now()->year;

                            $amount   = $subscription->price ?? $selectedPlan['price'] ?? $plan['price'] ?? null;
                            $currency = $subscription->currency ?? $selectedPlan['currency'] ?? $plan['currency'] ?? 'EUR';
                            if (is_numeric($amount)) {
                                $amountPretty = rtrim(rtrim(number_format($amount, 2, ',', '.'), '0'), ',');
                            } elseif ($amount) {
                                $amountPretty = (string) $amount;
                            } else {
                                $amountPretty = '—';
                            }

                            $dt   = $subscription->created_at ?? now();
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
                                    <td>{{ $paymentData['id'] }}</td>
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
                                                <strong>{{ $planData['name']->{current_locale()} ?? '—' }}</strong>
                                                {{--  @if(isset($planData['price']) && is_numeric($planData['price']))
                                                     <span class="text-muted">
                                                         — {{ rtrim(rtrim(number_format($planData['price'], 2, ',', '.'), '0'), ',') }}
                                                         {{ $planData['currency'] ?? 'EUR' }}
                                                     </span>
                                                 @endif --}}
                                                <a href="{{ localized_route('companies.payment') }}" class="btn btn-sm btn-outline-secondary ms-md-auto">
                                                    {{'Promijeni' }}
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
                        {{--<div class="d-flex mt-4 gap-2">

                            <a href="{{ localized_route('home') }}" class="btn btn-light">
                                {{ __('Početna') }}
                            </a>
                            @if(!empty($company))
                                <a href="{{ company_url($company) }}" class="btn btn-primary ms-auto">
                                    {{ __('Pogledaj profil:') }} {{ $companyName }}
                                </a>
                            @endif
                        </div>--}}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection