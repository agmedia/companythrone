@extends('layouts.app')
@section('title', $company->name)

@section('content')

    <!-- Breadcrumb -->
    <nav class="container pt-4 pb-2 pb-md-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ localized_route('home') }}">{{ __('nav.home') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ localized_route('companies.index') }}">{{ __('nav.companies') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $company->t_name }}</li>
        </ol>
    </nav>


    <!-- Event details -->
    <section class="container pb-3 pb-md-4 pb-lg-5 mb-xxl-3">
        <div class="row pb-5">

            <!-- Poster + Price + Action buttons -->
            <div class="col-sm-9 col-md-5 col-lg-4 pb-3 pb-sm-0 mb-4 mb-sm-5 mb-md-0">
                <div class=" bg-body-tertiary rounded overflow-hidden border" >
                    @if($company->is_published)
                        <div class="d-flex flex-column gap-2 align-items-start position-absolute top-0 start-0 z-3 pt-1 pt-sm-0 ps-1 ps-sm-0 mt-2 mt-sm-3 ms-2 ms-sm-3">
                        <span class="badge text-bg-info d-inline-flex">
                Provjerena tvrtka
                <i class="fi-shield ms-1"></i>
              </span>
                        </div>
                    @endif

                    <img src="{{ $company->getFirstMediaUrl('logo') }}" alt="{{ $company->t_name }}">
                </div>

            </div>


            <!-- Event info -->
            <div class="col-md-7 offset-lg-1">
                <div class="ps-md-4 ps-lg-0">

                    <!-- Event meta -->



                    <h1 class="display-6 mb-4">{{ $company->t_name }}</h1>
                    <ul class="list-unstyled gap-3 fs-sm pb-3 pb-sm-0 mb-3">



                            <li class="d-flex flex-wrap gap-2">
                                <div class="d-flex me-2">
                                    <i class="fi-map-pin fs-base me-2" style="margin-top: 3px"></i>
                                  Adresa:  {{ trim(($company->street ?? '').' '.($company->street_no ?? '')) }} {{ trim(($company->city ?? '').($company->city && $company->state ? ', ' : '').($company->state ?? '')) }}
                                </div>

                            </li>
                        <li class="d-flex">
                            <i class="fi-info fs-base me-2" style="margin-top: 3px"></i>
                            OIB:  {{ $company->oib }}
                        </li>

                        <li class="d-flex">
                            <i class="fi-phone-call fs-base me-2" style="margin-top: 3px"></i>
                            Telefon: <a href="tel:{{ $company->phone }}">{{ $company->phone }}</a>

                        </li>



                    </ul>



                    <!-- Description -->
                    <h2 class="h5 pt-2 pt-sm-0 ">Opis</h2>
                   <p> {!! $company->t_desc !!}  </p>

                    <!-- Organizer -->
                    <div class="d-flex flex-column flex-sm-row flex-md-column flex-lg-row align-items-center justify-content-start gap-3  pt-2  rounded p-0 mt-4">

                        <a href="{{$company->weburl}}" target="_blank" class="btn btn-primary"><i class="fi-link me-2"></i> Web stranica</a>


                        <div class="d-flex align-items-center gap-1 ">

                            <a href="#"
                               class="btn btn-outline-dark email-link"
                               data-email="{{ Crypt::encryptString($company->email) }}">
                                <i class="fi-mail me-2"></i> Pošaljite upit
                            </a>

                        </div>

                    </div>






                </div>
            </div>
        </div>
    </section>

    @if($featured->isNotEmpty())
    <!-- Sport events carousel -->
    <section class="container pb-5 my-xxl-3">
        <div class="d-flex align-items-start justify-content-between gap-4 pb-3 mb-2 mb-sm-3">
            <h2 class="mb-0">Iz kataloga</h2>
            <div class="nav">
                <a class="nav-link position-relative fs-base text-nowrap py-1 px-0" href="{{ localized_route('companies.index') }}">
                    <span class="hover-effect-underline stretched-link me-1">Pogledajte sve</span>
                    <i class="fi-chevron-right fs-lg"></i>
                </a>
            </div>
        </div>
        <div class="position-relative mx-3 mx-sm-0">

            <!-- Carousel -->
            <div class="swiper" data-swiper='{
            "slidesPerView": 1,
            "spaceBetween": 24,
            "loop": true,
            "autoHeight": true,
            "navigation": {
              "prevEl": "#sports-prev",
              "nextEl": "#sports-next"
            },
            "breakpoints": {
              "500": {
                "slidesPerView": 2
              },
              "800": {
                "slidesPerView": 3
              },
              "1100": {
                "slidesPerView": 4
              }
            }
          }'>
                <div class="swiper-wrapper">

                    @foreach($featured->take(6) as $c)

                        <!-- Event listing -->
                        <div class="swiper-slide">
                            <article class="card hover-effect-scale hover-effect-opacity bg-body-tertiary border-1">

                                <div class="bg-body-tertiary rounded overflow-hidden">
                                    <div class="hover-effect-target" >
                                        <img src="{{ $c->getFirstMediaUrl('logo') }}" alt="Image">
                                    </div>
                                </div>
                                <div class="card-body  p-3">
                                    <ul class="list-unstyled flex-row flex-wrap align-items-center gap-2 fs-sm pt-1 pt-sm-0 mb-2">
                                       {{--  <li class="d-flex align-items-center me-2">
                                            <i class="fi-calendar me-1"></i>
                                            {{ $c->published_at }}

                                        </li>--}}
                                        <li class="d-flex align-items-center me-2">
                                            <i class="fi-map-pin me-1"></i>
                                            @if($c->city) {{ $c->city }}@endif
                                        </li>
                                    </ul>
                                    <h3 class="h6 mb-0">
                                        <a class="hover-effect-underline stretched-link" href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale(), route('companies.show', ['companyBySlug' => $c->t_slug], false)) }}">{{ $c->t_name }}</a>
                                    </h3>
                                </div>
                            </article>
                        </div>

                    @endforeach
                </div>
            </div>

            <!-- Prev button -->
            <button type="button" class="btn btn-icon btn-outline-secondary animate-slide-start bg-body rounded-circle position-absolute top-50 start-0 translate-middle z-1 mt-n5" id="sports-prev" aria-label="Prev">
                <i class="fi-chevron-left fs-lg animate-target"></i>
            </button>

            <!-- Next button -->
            <button type="button" class="btn btn-icon btn-outline-secondary animate-slide-end bg-body rounded-circle position-absolute top-50 start-100 translate-middle z-1 mt-n5" id="sports-next" aria-label="Next">
                <i class="fi-chevron-right fs-lg animate-target"></i>
            </button>
        </div>
    </section>
    @endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.email-link').forEach(function(el) {
            el.addEventListener('click', function(event) {
                event.preventDefault();

                // Dekriptirati email na backendu putem AJAX-a
                fetch('/decrypt-email?data=' + el.dataset.email)
                    .then(response => response.json())
                    .then(data => {
                        window.location.href = 'mailto:' + data.email;
                    });
            });
        });
    });

</script>


@endsection



