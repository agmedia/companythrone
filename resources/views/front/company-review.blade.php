@extends('layouts.app')
@section('title', __('company.add'))

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
                        <h1 class="h2 mb-3">{{ __('company.review') }}</h1>
                        <form method="post" enctype="multipart/form-data" action="{{ localized_route('companies.success') }}" class="vstack gap-3">
                            @csrf
                            <!-- Dark table with striped columns -->
                            <div class="table-responsive">
                                <table class="table table-striped-columns">
                                    <tbody>
                                    <tr>
                                        <th class="w-25 text-nowrap">Naziv tvrtke: </th>
                                        <td>A.G media d.o.o.</td>
                                    </tr>
                                    <tr>
                                        <th class="w-25 text-nowrap">OIB:</th>
                                        <td>33539839250a</td>
                                    </tr>
                                    <tr>
                                        <th class="w-25 text-nowrap">E-mail:</th>
                                        <td>info@agmedia.hr</td>
                                    </tr>
                                    <tr>
                                        <th class="w-25 text-nowrap">Web stranica:</th>
                                        <td>info@agmedia.hr</td>
                                    </tr>
                                    <tr>
                                        <th class="w-25 text-nowrap">Kratki opis:</th>
                                        <td>S preko 20 godina iskustva i in-house timom izrađujemo cjelovita internet rješenja (B2C ili B2B) koja se ističu svojom brinom i dizajnom. Uz dugogodišnje iskustvo u izradi svih vrsta web projekata redovito se educiramo, usavršavamo kako bi adekvatno mogli usvajati i primjenjivati sve nove trendove u struci.</td>
                                    </tr>
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
