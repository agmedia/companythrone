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

                <fieldset class="border-bottom py-4" role="radiogroup" aria-label="Ad review options">
                    <div class="row align-items-center py-md-1 py-lg-2 py-xl-3">
                        <div class="col-12 col-md-5">
                            <div class="d-flex align-items-center gap-2">
                                <input
                                    type="radio"
                                    class="form-check-input switch-radio"
                                    name="certify"
                                    id="certify_yes"
                                    value="1"
                                    required
                                >
                                <label for="certify_yes" class="form-check-label h6 fs-6 mb-0">
                                    Plaćenje karticama - Corvus Pay
                                </label>
                            </div>
                        </div>
                        <div class="col-8 col-md-5">
                            <p class="fs-sm mb-0">Plaćanje kreditnim i debitnim karticama, Google Pay, Apple Pay</p>
                        </div>
                        <div class="col-4 col-md-2">
                            <div class="h5 text-end text-nowrap mb-0">25€  <span class="fs-sm ">/ godišnje</span> </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="border-bottom py-4">
                    <div class="row align-items-center py-md-1 py-lg-2 py-xl-3">
                        <div class="col-12 col-md-5">
                            <div class="d-flex align-items-center gap-2">
                                <input
                                    type="radio"
                                    class="form-check-input switch-radio"
                                    name="certify"
                                    id="certify_no"
                                    value="0"
                                >
                                <label for="certify_no" class="form-check-label h6 fs-6 mb-0">

                                    Općom uplatnicom / Virmanom / Internet bankarstvom
                                </label>
                            </div>
                        </div>
                        <div class="col-8 col-md-5">
                            <p class="fs-sm mb-0">Uplatite direktno na naš bankovni račun. Uputstva i uplatnice vam stiže putem maila.</p>
                        </div>
                        <div class="col-4 col-md-2">
                            <div class="h5 text-end text-nowrap mb-0">25€  <span class="fs-sm ">/ godišnje</span> </div>
                        </div>
                    </div>
                </fieldset>

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

