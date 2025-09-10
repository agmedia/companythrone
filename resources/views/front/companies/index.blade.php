@extends('layouts.app')

@section('title', __('company.list_title'))

{{-- Ova stranica treba Swiper/Choices/Simplebar – ubaci ako nisu globalno u layoutu --}}
@section('head')
    <link rel="stylesheet" href="/theme1/assets/vendor/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="/theme1/assets/vendor/choices.js/public/assets/styles/choices.min.css">
    <link rel="stylesheet" href="/theme1/assets/vendor/simplebar/dist/simplebar.min.css">
@endsection

@section('content')
    <main class="content-wrapper">
        <div class="container pt-4 pb-5 mb-xxl-3">

            {{-- Breadcrumb --}}
            <nav class="pb-2 pb-md-3" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ localized_route('home') }}">{{ __('nav.home') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('company.list_title') }}</li>
                </ol>
            </nav>

            {{-- Sidebar filter + Listing --}}
            <div class="row pb-2 pb-sm-3 pb-md-4 pb-lg-5">

                {{-- FILTER SIDEBAR (offcanvas < lg) --}}
                <aside class="col-lg-3">
                    <div class="offcanvas-lg offcanvas-start pe-lg-2 pe-xl-3 pe-xxl-4" id="filterSidebar">
                        <div class="offcanvas-header border-bottom py-3">
                            <h3 class="h5 offcanvas-title">{{ __('company.filters') }}</h3>
                            <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#filterSidebar" aria-label="Close"></button>
                        </div>

                        <div class="offcanvas-body d-block">
                            <form method="get" action="{{ localized_route('companies.index') }}" class="vstack gap-4">

                                {{-- Pretraga --}}
                                <div>
                                    <h4 class="h6 mb-2">{{ __('company.search_label') }}</h4>
                                    <div class="position-relative">
                                        <i class="fi-search position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                                        <input type="search"
                                               class="form-control form-icon-start"
                                               id="q"
                                               name="q"
                                               value="{{ $q }}"
                                               placeholder="{{ __('company.search_placeholder') }}">
                                    </div>
                                </div>

                                {{-- Kategorija (Choices) --}}
                                <div>
                                    <h4 class="h6 mb-2">{{ __('company.category') }}</h4>
                                    <div class="position-relative">
                                        <i class="fi-layers position-absolute top-50 start-0 translate-middle-y z-1 ms-3"></i>
                                        <select class="form-select form-icon-start"
                                                id="category"
                                                name="category"
                                                data-select='{
                              "searchEnabled": true,
                              "allowHTML": false,
                              "classNames": { "containerInner": ["form-select","form-icon-start"] }
                            }'
                                                aria-label="{{ __('company.category') }}">
                                            <option value="">{{ __('company.all_categories') }}</option>
                                            @foreach($categories as $c)
                                                <option value="{{ $c->slug }}" @selected($cat === $c->slug)>{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Gumbi --}}
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary" type="submit">{{ __('company.apply_filters') }}</button>
                                    <a class="btn btn-outline-secondary" href="{{ localized_route('companies.index') }}">{{ __('company.reset') }}</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </aside>

                {{-- LISTINGS --}}
                <div class="col-lg-9">

                    {{-- Aktivni filteri i "Clear all" --}}
                    @if($q || $cat)
                        <div class="d-flex align-items-center gap-3 pb-sm-2">
                            <div class="w-100 pb-3 overflow-x-auto">
                                <div class="d-flex gap-2">
                                    @if($q)
                                        <a class="btn btn-sm btn-secondary rounded-pill" href="{{ localized_route('companies.index', collect(request()->except('q'))->toArray()) }}">
                                            <i class="fi-close fs-sm me-1 ms-n1"></i> {{ $q }}
                                        </a>
                                    @endif
                                    @if($cat)
                                        <a class="btn btn-sm btn-secondary rounded-pill" href="{{ localized_route('companies.index', collect(request()->except('category'))->toArray()) }}">
                                            <i class="fi-close fs-sm me-1 ms-n1"></i> {{ optional($categories->firstWhere('slug',$cat))->name ?? $cat }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="nav pb-3">
                                <a class="nav-link fs-xs text-decoration-underline text-nowrap p-0" href="{{ localized_route('companies.index') }}">{{ __('company.reset') }}</a>
                            </div>
                        </div>
                    @endif

                    {{-- Sort --}}
                    <div class="d-flex align-items-center gap-2 gap-sm-3 pb-3 mb-2">
                        <div class="fs-sm text-nowrap">{{ trans_choice('company.results_count', $companies->total(), ['count'=>$companies->total()]) }}</div>

                        <form method="get" action="{{ localized_route('companies.index') }}" class="position-relative ms-auto" style="width: 220px">
                            {{-- zadrži postojeće upite --}}
                            <input type="hidden" name="q" value="{{ $q }}">
                            <input type="hidden" name="category" value="{{ $cat }}">
                            <i class="fi-sort position-absolute top-50 start-0 translate-middle-y z-2"></i>
                            <select class="form-select border-0 rounded-0 ps-4 pe-1"
                                    id="sort"
                                    name="sort"
                                    data-select='{ "removeItemButton": false, "classNames": { "containerInner": ["form-select","border-0","rounded-0","ps-4","pe-1"] } }'
                                    onchange="this.form.submit()">
                                <option value="newest" @selected($sort==='newest')>{{ __('company.sort_newest') }}</option>
                                <option value="name_asc" @selected($sort==='name_asc')>{{ __('company.sort_name_asc') }}</option>
                                <option value="name_desc" @selected($sort==='name_desc')>{{ __('company.sort_name_desc') }}</option>
                                <option value="random" @selected($sort==='random')>{{ __('company.sort_random') }}</option>
                            </select>
                        </form>
                    </div>

                    {{-- LISTA – Finder stil, dinamički --}}
                    <div class="vstack gap-4">
                        @forelse($companies as $c)
                            <article class="card hover-effect-opacity overflow-hidden">
                                <div class="row g-0">

                                    {{-- Lijevo: slika/slider (Swiper) --}}
                                    <div class="col-sm-4 position-relative bg-body-tertiary" style="min-height: 220px">
                                        @if($c->is_published)
                                            <div class="d-flex flex-column gap-2 align-items-start position-absolute top-0 start-0 z-3 pt-1 ps-1 mt-2 ms-2">
                                                <span class="badge text-bg-info d-inline-flex align-items-center">
                                                  {{ __('company.verified') ?? 'Verified' }}
                                                  <i class="fi-shield ms-1"></i>
                                                </span>
                                            </div>
                                        @endif

                                        <div class="swiper h-100 z-2" data-swiper='{
                                                  "pagination": { "el": ".swiper-pagination" },
                                                  "navigation": { "prevEl": ".btn-prev", "nextEl": ".btn-next" },
                                                  "breakpoints": { "991": { "allowTouchMove": false } }
                                                }'>
                                            {{-- TODO: Kad uvedemo galeriju (media collection "images"), ovdje petljaj po slici; sada 1 fallback --}}
                                            <a class="swiper-wrapper h-100" href="{{ localized_route('companies.show', ['companyBySlug' => $c->t_slug]) }}">
                                                <div class="swiper-slide">
                                                    @php
                                                        $cover = method_exists($c,'hasMedia') && $c->hasMedia('logo')
                                                                 ? $c->getFirstMediaUrl('logo')
                                                                 : '/theme1/assets/img/default_image.jpg';
                                                    @endphp
                                                    <img src="{{ $cover }}" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover" alt="{{ $c->t_name ?? $c->name ?? 'Company' }}">
                                                    <span class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(180deg, rgba(0,0,0, 0) 0%, rgba(0,0,0, .16) 100%)"></span>
                                                </div>
                                            </a>

                                            {{-- Prev/Next (desktop) --}}
                                            <div class="position-absolute top-50 start-0 z-1 translate-middle-y d-none d-lg-block hover-effect-target opacity-0 ms-3">
                                                <button type="button" class="btn btn-sm btn-prev btn-icon btn-light bg-light rounded-circle animate-slide-start" aria-label="Prev">
                                                    <i class="fi-chevron-left fs-lg animate-target"></i>
                                                </button>
                                            </div>
                                            <div class="position-absolute top-50 end-0 z-1 translate-middle-y d-none d-lg-block hover-effect-target opacity-0 me-3">
                                                <button type="button" class="btn btn-sm btn-next btn-icon btn-light bg-light rounded-circle animate-slide-end" aria-label="Next">
                                                    <i class="fi-chevron-right fs-lg animate-target"></i>
                                                </button>
                                            </div>

                                            <div class="swiper-pagination bottom-0 z-1 mb-2" data-bs-theme="light"></div>
                                        </div>
                                    </div>

                                    {{-- Desno: detalji --}}
                                    <div class="col-sm-8 d-flex p-3 p-sm-4" style="min-height: 255px">
                                        <div class="row flex-lg-nowrap g-0 position-relative pt-1 pt-sm-0">

                                            {{-- Bookmark (dummy) --}}
                                            <button type="button" class="btn btn-icon btn-outline-secondary rounded-circle position-absolute top-0 end-0 z-2" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-sm" title="Bookmark" aria-label="Bookmark">
                                                <i class="fi-bookmark fs-base"></i>
                                            </button>

                                            {{-- Naziv, avatar, kratko --}}
                                            <div class="col-lg-8 pe-lg-4">
                                                <div class="d-flex align-items-center pe-5 pe-lg-0 pb-2 mb-1">
                                                    <div class="ratio ratio-1x1 me-3" style="width: 48px">
                                                        <img src="{{ method_exists($c,'hasMedia') && $c->hasMedia('logo') ? $c->getFirstMediaUrl('logo') : '/theme1/assets/img/default_image.jpg' }}"
                                                             class="bg-body-secondary rounded-circle object-fit-cover" alt="Avatar">
                                                    </div>
                                                    <h3 class="h6 mb-0">
                                                        {{-- TODO: zamijeni # s lokaliziranom rutom kad završimo binding (npr. localized_route('companies.show', ['locale'=>app()->getLocale(),'slug'=>$c->t_slug])) --}}
                                                        <a class="hover-effect-underline stretched-link" href="#">{{ $c->t_name ?? $c->name ?? 'Company' }}</a>
                                                    </h3>
                                                </div>

                                                @if(!empty($c->slogan))
                                                    <div class="fs-sm mb-2 mb-lg-3">
                                                        <span class="fw-medium text-dark-emphasis">{{ $c->slogan }}</span>
                                                    </div>
                                                @endif

                                                @php $desc = $c->description ?? null; @endphp
                                                @if($desc)
                                                    <p class="fs-sm mb-0 text-body">{{ \Illuminate\Support\Str::limit(strip_tags($desc), 180) }}</p>
                                                @else
                                                    <p class="fs-sm mb-0 text-body">
                                                        {{ trim($c->city.' '.$c->state) }}
                                                    </p>
                                                @endif
                                            </div>

                                            <hr class="vr flex-shrink-0 d-none d-lg-block m-0">

                                            {{-- Desni stupac: značke / akcija --}}
                                            <div class="col-lg-4 d-flex flex-column pt-3 pt-lg-5 ps-lg-4">
                                                <ul class="list-unstyled pb-2 pb-lg-4 mb-3">
                                                    @if($c->is_link_active)
                                                        <li class="d-flex align-items-center gap-1 fs-sm">
                                                            <i class="fi-thumbs-up"></i> {{ __('company.link_active') ?? 'Link active' }}
                                                        </li>
                                                    @else
                                                        <li class="d-flex align-items-center gap-1 fs-sm">
                                                            <i class="fi-clock"></i> {{ __('company.link_inactive') }}
                                                        </li>
                                                    @endif
                                                </ul>

                                                {{-- “Connect” je placeholder – može kasnije otvoriti modal ili ići na kontakt --}}
                                                <a href="#" class="btn btn-outline-dark position-relative z-2 mt-auto">
                                                    <i class="fi-mail me-2"></i> Connect
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="alert alert-info mb-0">{{ __('company.empty_results') }}</div>
                        @endforelse
                    </div>

                    {{-- Paginacija --}}
                    <nav class="pt-3 mt-3" aria-label="Listings pagination">
                        {{ $companies->links() }}
                    </nav>
                </div>
            </div>
        </div>
    </main>

    {{-- Offcanvas toggle na mobitelu (< lg) --}}
    <button type="button"
            class="fixed-bottom z-sticky w-100 btn btn-lg btn-dark border-0 border-top border-light border-opacity-10 rounded-0 pb-4 d-lg-none"
            data-bs-toggle="offcanvas" data-bs-target="#filterSidebar" aria-controls="filterSidebar" data-bs-theme="light">
        <i class="fi-sidebar fs-base me-2"></i> {{ __('company.filters') }}
    </button>
@endsection

@push('scripts')
    {{-- Vendor za ovu stranicu, ako nisu globalno u layoutu --}}
    <script src="/theme1/assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="/theme1/assets/vendor/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="/theme1/assets/vendor/simplebar/dist/simplebar.min.js"></script>
    <script src="/theme1/assets/js/theme.min.js"></script>
@endpush
