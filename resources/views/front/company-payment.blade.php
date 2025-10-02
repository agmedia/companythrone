@extends('layouts.app')
@section('title', __('company.add'))
@section('content')

    @include('components.layouts.app..checkout-steps-nav')


<div class="container-xxl py-4">

  <div class="row justify-content-start">
    <div class="col-lg-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h1 class="h2 mb-3">{{ __('company.payment') }}</h1>
            <form method="post" enctype="multipart/form-data" action="{{ localized_route('companies.review') }}" class="vstack gap-3">
                @csrf

                @foreach($payments as $p)
                    <fieldset class="border-bottom py-4" role="radiogroup" aria-label="Ad review options">
                        <div class="row align-items-center py-md-1 py-lg-2 py-xl-3">
                            <div class="col-12 col-md-5">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="radio" @checked(($selectedCode ?? null) === $p['code']) class="form-check-input switch-radio" name="plan" value="{{ $p['code'] }}" id="certify-{{ $p['code'] }}" required>
                                    <label for="certify_yes" class="form-check-label h6 fs-6 mb-0">{{ $p['name'] }}</label>
                                </div>
                            </div>
                            <div class="col-8 col-md-5">
                                @if(!empty($p['short_description']))
                                    @if (is_string($p['short_description']))
                                        <p class="fs-sm mb-0">{{ $p['short_description'] ?: '' }}</p>
                                    @else
                                        <p class="fs-sm mb-0">{{ $p['short_description'][current_locale()] ?: '' }}</p>
                                    @endif
                                @endif
                            </div>
                            <div class="col-4 col-md-2">
                                @if(($p['display_price_gross'] ?? 0) > 0)
                                    <div class="h5 text-end text-nowrap mb-0">
                                        {{ rtrim(rtrim(number_format($p['display_price_gross'], 2, '.', ''), '0'), '.') }}
                                        {{ $p['display_currency'] ?? 'EUR' }}
                                        <span class="fs-sm ">
      / {{ ($p['display_period'] ?? 'yearly') === 'monthly' ? __('mjesečno') : __('godišnje') }}
    </span>
                                    </div>
                                @else
                                    <div class="h5 text-end text-nowrap mb-0">
                                        <span class="fs-sm ">{{ __('Besplatno') }}</span>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </fieldset>
                @endforeach

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

