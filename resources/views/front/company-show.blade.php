@extends('layouts.app')
@section('title', $company->name)

@section('content')
    <div class="container pt-4 pb-5 mb-xxl-3">

        {{-- Breadcrumb --}}
        <nav class="pb-2 pb-md-3" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ localized_route('home') }}">{{ __('nav.home') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $company->t_name }}</li>
            </ol>
        </nav>

        {{-- Header: avatar + meta + bookmark --}}
        <div class="d-flex align-items-start align-items-sm-center justify-content-between pb-3 mb-3">
            <div class="d-sm-flex align-items-center me-4">
                <div class="ratio ratio-1x1 flex-shrink-0 bg-body-secondary rounded-circle overflow-hidden mb-2 mb-sm-0" style="width: 72px">
                    @if($company->hasMedia('logo'))
                        <img src="{{ $company->getFirstMediaUrl('logo') }}" alt="{{ $company->t_name }}">
                    @else
                        <div class="d-flex w-100 h-100 align-items-center justify-content-center fw-semibold">
                            {{ mb_strtoupper(mb_substr($company->t_name,0,1)) }}
                        </div>
                    @endif
                </div>
                <div class="ps-sm-3 ps-md-4">
                    <div class="d-flex align-items-center pb-1 mb-2">
                        <h1 class="h5 pe-1 mb-0 me-2">{{ $company->t_name }}</h1>
                        @if($company->is_published)
                            <span class="badge text-bg-info d-inline-flex">
                Verified
                <i class="fi-shield ms-1"></i>
              </span>
                        @endif
                    </div>

                    {{-- Meta linija: lokacija, telefon, e-mail (umjesto ratinga / cjenovnog raspona iz HTML-a) --}}
                    <ul class="list-inline gap-2 fs-sm ms-n2 mb-0">
                        @if($company->city || $company->state)
                            <li class="d-flex align-items-center gap-1 ms-2">
                                <i class="fi-map-pin"></i>
                                {{ trim(($company->city ?? '').($company->city && $company->state ? ', ' : '').($company->state ?? '')) }}
                            </li>
                        @endif
                        @if($company->phone)
                            <li class="d-flex align-items-center gap-1 ms-2">
                                <i class="fi-phone"></i>{{ $company->phone }}
                            </li>
                        @endif
                        @if($company->email)
                            <li class="d-flex align-items-center gap-1 ms-2">
                                <i class="fi-mail"></i>{{ $company->email }}
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <button type="button" class="btn btn-icon btn-outline-secondary rounded-circle" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-sm" title="Bookmark" aria-label="Bookmark">
                <i class="fi-bookmark fs-base"></i>
            </button>
        </div>

        {{-- Gallery (Media Library: collection "gallery") --}}
        @php
            $gallery = $company->getMedia('gallery');
        @endphp
        <div class="row g-3 g-sm-4 g-md-3 g-xl-4 pb-sm-2 mb-5">
            @if($gallery->count())
                <div class="col-md-8">
                    <a class="hover-effect-scale hover-effect-opacity position-relative d-flex rounded overflow-hidden"
                       href="{{ $gallery[0]->getUrl() }}" data-glightbox data-gallery="image-gallery">
                        <i class="fi-zoom-in hover-effect-target fs-3 text-white position-absolute top-50 start-50 translate-middle opacity-0 z-2"></i>
                        <span class="hover-effect-target position-absolute top-0 start-0 w-100 h-100 bg-black bg-opacity-25 opacity-0 z-1"></span>
                        <div class="ratio hover-effect-target bg-body-tertiary rounded" style="--fn-aspect-ratio: calc(432 / 856 * 100%)">
                            <img src="{{ $gallery[0]->getUrl('') }}" alt="{{ $company->t_name }}">
                        </div>
                    </a>
                </div>
                <div class="col-md-4">
                    <div class="row row-cols-2 g-3 g-sm-4 g-md-3 g-xl-4">
                        @foreach($gallery->slice(1, 4) as $media)
                            <div class="col">
                                <a class="hover-effect-scale hover-effect-opacity position-relative d-flex rounded overflow-hidden"
                                   href="{{ $media->getUrl() }}" data-glightbox data-gallery="image-gallery">
                                    <i class="fi-zoom-in hover-effect-target fs-3 text-white position-absolute top-50 start-50 translate-middle opacity-0 z-2"></i>
                                    <span class="hover-effect-target position-absolute top-0 start-0 w-100 h-100 bg-black bg-opacity-25 opacity-0 z-1"></span>
                                    <div class="ratio hover-effect-target bg-body-tertiary rounded" style="--fn-aspect-ratio: calc(204 / 196 * 100%)">
                                        <img src="{{ $media->getUrl('') }}" alt="{{ $company->t_name }}">
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Fallback bez galerije --}}
                <div class="col-12">
                    <div class="bg-body-tertiary rounded d-flex align-items-center justify-content-center" style="height: 320px">
                        <div class="text-center">
                            <i class="fi-image fs-2 d-block mb-2"></i>
                            <div class="text-body-secondary">No gallery yet</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Content + Sidebar --}}
        <div class="row pb-2 pb-sm-3 pb-md-4 pb-lg-5">

            {{-- Left: sections --}}
            <div class="col-lg-8 col-xl-7">

                {{-- About --}}
                <section class="pb-sm-2 pb-lg-3 mb-5">
                    <h2 class="h4 mb-lg-4">About</h2>
                    <p class="fs-sm mb-3">
                        {{-- Nemamo opis u modelu; kratki auto-opis iz dostupnih polja --}}
                        {{ $company->t_name }}
                        @if($company->city || $company->state)
                            — {{ trim(($company->city ?? '').($company->city && $company->state ? ', ' : '').($company->state ?? '')) }}
                        @endif
                    </p>

                    {{-- Dnevne akcije (postojeća Livewire komponenta) --}}
                    @livewire('front.daily-buttons', ['company'=>$company])
                </section>

                {{-- Services offered = kategorije --}}
                @php
                    $cats = isset($categories) ? $categories : ($company->relationLoaded('categories') ? $company->categories : $company->categories()->get());
                @endphp
                @if($cats->count())
                    <section class="pb-sm-2 pb-lg-3 mb-5">
                        <h2 class="h4 mb-4">Services offered</h2>
                        <div class="row row-cols-2 row-cols-sm-3 gy-3 fs-sm">
                            @foreach($cats as $cat)
                                <div class="col d-flex">
                                    <i class="fi-check-circle fs-xl me-2"></i>
                                    {{ $cat->name }}
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Projects (Media Library: collection "projects") --}}
                @php $projects = $company->getMedia('projects'); @endphp
                @if($projects->count())
                    <section class="pb-sm-2 pb-lg-3 mb-5">
                        <h2 class="h4 mb-sm-4">{{ $projects->count() }} Projects</h2>
                        <ul class="nav nav-pills flex-nowrap gap-2 overflow-x-auto text-nowrap pb-3 mb-2 mb-sm-3">
                            <li class="nav-item me-1"><span class="nav-link active">All</span></li>
                        </ul>
                        <div class="row row-cols-2 gy-4 gx-3 gx-sm-4">
                            @foreach($projects as $media)
                                <div class="col mb-2">
                                    <article class="card hover-effect-scale bg-transparent border-0">
                                        <div class="bg-body-tertiary rounded overflow-hidden">
                                            <a class="ratio hover-effect-target" href="{{ $media->getUrl() }}" data-glightbox data-gallery="projects" style="--fn-aspect-ratio: calc(240 / 360 * 100%)">
                                                <img src="{{ $media->getUrl('') }}" alt="{{ $media->name }}">
                                            </a>
                                        </div>
                                        <div class="card-body pt-3 pt-sm-4 pb-2 px-0">
                                            <h3 class="h6 mb-0">{{ $media->getCustomProperty('title') ?? $media->name }}</h3>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Business details --}}
                <section class="pb-sm-2 pb-lg-3 mb-5">
                    <h2 class="h4 mb-4">Business details</h2>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                        <div class="col">
                            <h5 class="h6 fs-sm pb-1 mb-2">General info</h5>
                            <ul class="list-unstyled fs-sm mb-0">
                                <li>Business name: <span class="fw-medium text-dark-emphasis">{{ $company->t_name }}</span></li>
                                <li>Level: <span class="fw-medium text-dark-emphasis">{{ optional($company->level)->number ?? '—' }}</span></li>
                                <li>OIB: <span class="fw-medium text-dark-emphasis">{{ $company->oib }}</span></li>
                            </ul>
                        </div>
                        <div class="col">
                            <h5 class="h6 fs-sm pb-1 mb-2">Contacts</h5>
                            <ul class="list-unstyled fs-sm mb-0">
                                @if($company->street || $company->street_no)
                                    <li>{{ trim(($company->street ?? '').' '.($company->street_no ?? '')) }}</li>
                                @endif
                                @if($company->city || $company->state)
                                    <li>{{ trim(($company->city ?? '').($company->city && $company->state ? ', ' : '').($company->state ?? '')) }}</li>
                                @endif
                                @if($company->phone)
                                    <li>{{ $company->phone }}</li>
                                @endif
                                @if($company->email)
                                    <li>{{ $company->email }}</li>
                                @endif
                            </ul>
                        </div>
                        <div class="col">
                            <h5 class="h6 fs-sm pb-1 mb-2">Working hours</h5>
                            <ul class="list-unstyled fs-sm mb-0">
                                <li>—</li>
                            </ul>
                        </div>
                    </div>
                </section>

                {{-- Reviews (stub) --}}
                <section class="pt-sm-1 pt-md-3 pt-lg-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h2 class="h4 mb-0">Reviews</h2>
                        <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#reviewForm">
                            <i class="fi-edit-3 fs-base ms-n1 me-2"></i>
                            Add review
                        </button>
                    </div>
                    <div class="fs-sm text-body-secondary">No reviews yet.</div>
                </section>
            </div>

            {{-- Sidebar (contact) --}}
            <aside class="col-lg-4 offset-xl-1" style="margin-top: -105px">
                <div class="offcanvas-lg offcanvas-end sticky-lg-top" id="contactForm">
                    <div class="d-none d-lg-block" style="height: 105px"></div>
                    <div class="offcanvas-header border-bottom py-3">
                        <h3 class="h5 offcanvas-title">Contact {{ $company->t_name }}</h3>
                        <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#contactForm" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body d-block position-relative p-lg-4">
                        <div class="position-relative z-1 py-lg-2 px-xl-2">
                            <h4 class="h5 text-center d-none d-lg-block pb-1 mb-2">Contact {{ $company->t_name }}</h4>
                            <p class="fs-sm text-lg-center mb-4">Please fill out the form to contact a specialist</p>
                            <form class="needs-validation" novalidate @if($company->email) action="mailto:{{ $company->email }}" @endif>
                                @csrf
                                <div class="mb-3">
                                    <input type="text" class="form-control" placeholder="Name *" required>
                                    <div class="invalid-feedback">Please enter your name!</div>
                                </div>
                                <div class="mb-3">
                                    <input type="tel" class="form-control" data-input-format='{"numericOnly": true, "delimiters": ["+ ", " ", " "], "blocks": [0, 3, 3, 2]}' placeholder="Phone number *" required>
                                    <div class="invalid-feedback">Please enter your phone number!</div>
                                </div>
                                <div class="mb-3">
                                    <input type="text" class="form-control" placeholder="Zip code">
                                </div>
                                <div class="mb-4">
                                    <textarea class="form-control" rows="5" placeholder="Message *" required></textarea>
                                    <div class="invalid-feedback">Please write your message!</div>
                                </div>
                                <button type="submit" class="btn btn-lg btn-primary w-100">Send message</button>
                            </form>
                        </div>
                        <span class="position-absolute top-0 start-0 w-100 h-100 bg-body-tertiary rounded d-none d-lg-block"></span>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    {{-- Review modal (UI stub, bez backend logike) --}}
    <div class="modal fade" id="reviewForm" data-bs-backdrop="static" tabindex="-1" aria-labelledby="reviewFormLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content needs-validation" novalidate>
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="reviewFormLabel">Leave a review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pb-3 pt-0">
                    <div class="mb-3">
                        <label for="review-name" class="form-label">Your name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="review-name" required>
                        <div class="invalid-feedback">Please enter your name!</div>
                    </div>
                    <div class="mb-3">
                        <label for="review-email" class="form-label">Your email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="review-email" required>
                        <div class="invalid-feedback">Please provide valid email address!</div>
                    </div>
                    <div>
                        <label class="form-label" for="review-text">Review <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="4" id="review-text" required></textarea>
                        <div class="invalid-feedback">Please write a review!</div>
                    </div>
                </div>
                <div class="modal-footer flex-nowrap gap-3 border-0 px-4">
                    <button type="reset" class="btn btn-secondary w-100 m-0" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary w-100 m-0">Submit review</button>
                </div>
            </form>
        </div>
    </div>
@endsection
