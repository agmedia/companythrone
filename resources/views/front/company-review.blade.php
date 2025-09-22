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

