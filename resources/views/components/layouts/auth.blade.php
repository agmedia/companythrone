<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}" data-bs-theme="light" data-pwa="false">

<head>
    <meta charset="utf-8">

    <!-- Viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover">

    <!-- SEO Meta Tags -->
    <title>@yield('title', config('app.name','Companythrone'))</title>
    <meta name="description" content="@yield('description', config('app.description','Companythrone'))">
    <meta name="keywords" content="@yield('keywords', config('app.keywords','Companythrone'))">
    <meta name="author" content="@yield('title', config('app.name','Companythrone'))">

    <!-- Webmanifest + Favicon / App icons -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <!-- CSS -->
    <link rel="manifest" href="{{ asset('theme1/manifest.json') }}">
    <link rel="icon" type="image/png" href="{{ asset('theme1/assets/app-icons/icon-32x32.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('theme1/assets/app-icons/icon-180x180.png') }}">



    <!-- Preloaded local web font (Inter) -->
    <link rel="preload" href="{{ asset('theme1/assets/fonts/inter-variable-latin.woff2') }}" as="font" type="font/woff2" crossorigin>

    <!-- Font icons -->
    <link rel="preload" href="{{ asset('theme1/assets/icons/finder-icons.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('theme1/assets/icons/finder-icons.min.css') }}">

    <!-- Vendor styles -->
    <link rel="stylesheet" href="{{ asset('theme1/assets/vendor/swiper/swiper-bundle.min.css') }}">

    <!-- Bootstrap + Theme styles -->
    <link rel="preload" href="{{ asset('theme1/assets/theme.min.css') }}" as="style">
    <link rel="preload" href="{{ asset('theme1/assets/theme.rtl.min.css') }}" as="style">
    <link rel="stylesheet" href="{{ asset('theme1/assets/css/theme.min.css') }}" id="theme-styles">

    @livewireStyles
</head>

<body>

<!-- Page content -->
<main class="content-wrapper w-100 px-3 ps-lg-5 pe-lg-4 mx-auto" style="max-width: 1920px">
    <div class="d-lg-flex">

        <!-- Login form + Footer -->
        <div class="d-flex flex-column min-vh-100 w-100 py-4 mx-auto me-lg-5" style="max-width: 416px">

            <!-- Logo -->
            <header class="navbar px-0 pb-4 mt-n2 mt-sm-0 mb-2 mb-md-3 mb-lg-4">
                <a class="navbar-brand pt-0" href="{{ localized_route('home') }}">
          <span class=" d-flex flex-shrink-0 text-primary rtl-flip me-2">
          <img src="{{ asset('theme1/assets/companythrone-round.svg') }}" alt="Companythrone">
          </span>
                    <span class="d-none d-sm-flex fs-4 ">
            {{ config('app.name','Companythrone') }}
            </span>
                </a>
            </header>

<div class="mt-auto mb-auto">

            <!-- Form -->
            {{$slot}}
</div>


        </div>


        <!-- Cover image visible on screens > 992px wide (lg breakpoint) -->
        <div class="offcanvas-lg offcanvas-end w-100 py-lg-4 ms-auto" id="benefits" style="max-width: 1034px">
            <div class="offcanvas-header justify-content-end position-relative z-2 p-3">
                <button type="button" class="btn btn-icon btn-outline-dark text-dark border-dark bg-transparent rounded-circle d-none-dark" data-bs-dismiss="offcanvas" data-bs-target="#benefits" aria-label="Close">
                    <i class="fi-close fs-lg"></i>
                </button>
                <button type="button" class="btn btn-icon btn-outline-dark text-light border-light bg-transparent rounded-circle d-none d-inline-flex-dark" data-bs-dismiss="offcanvas" data-bs-target="#benefits" aria-label="Close">
                    <i class="fi-close fs-lg"></i>
                </button>
            </div>
            <span class="position-absolute top-0 start-0 w-100 h-100 bg-info-subtle d-lg-none"></span>
            <div class="offcanvas-body position-relative z-2 d-lg-flex flex-column align-items-center justify-content-center h-100 pt-2 px-3 p-lg-0">
                <span class="position-absolute top-0 start-0 w-100 h-100 bg-info-subtle rounded-5 d-none d-lg-block"></span>
                <div class="position-relative z-2 w-100 text-center px-md-2 p-lg-5">
                    <h2 class="h4 pb-3">Prednosti registracije</h2>
                    <div class="mx-auto" style="max-width: 790px">
                        <div class="row row-cols-1 row-cols-sm-2 g-3 g-md-4 g-lg-3 g-xl-4">

                            <!-- SEO nagrade -->
                            <div class="col">
                                <div class="card h-100 bg-transparent border-0">
                                    <span class="position-absolute top-0 start-0 w-100 h-100 bg-white border border-white border-opacity-75 rounded-4 d-none-dark" style="--fn-bg-opacity: .3"></span>
                                    <span class="position-absolute top-0 start-0 w-100 h-100 bg-white border rounded-4 d-none d-block-dark" style="--fn-bg-opacity: .05"></span>
                                    <div class="card-body position-relative z-2">
                                        <div class="d-inline-flex position-relative text-info p-3">
                                            <span class="position-absolute top-0 start-0 w-100 h-100 bg-white rounded-pill d-none-dark"></span>
                                            <span class="position-absolute top-0 start-0 w-100 h-100 bg-body-secondary rounded-pill d-none d-block-dark"></span>
                                            <i class="fi-gift position-relative z-2 fs-4 m-1"></i>
                                        </div>
                                        <h3 class="h6 pt-2 my-2">Jačanje online prisutnosti</h3>
                                        <p class="fs-sm mb-0">Aktivne tvrtke mogu ostvariti veću vidljivost i izgraditi svoj online
                                            autoritet. Kvalitetne poveznice  jedan su od ključnih čimbenika koji
                                            tražilice cijene pri rangiranju.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Ekskluzivne promocije -->
                            <div class="col">
                                <div class="card h-100 bg-transparent border-0">
                                    <span class="position-absolute top-0 start-0 w-100 h-100 bg-white border border-white border-opacity-75 rounded-4 d-none-dark" style="--fn-bg-opacity: .3"></span>
                                    <span class="position-absolute top-0 start-0 w-100 h-100 bg-white border rounded-4 d-none d-block-dark" style="--fn-bg-opacity: .05"></span>
                                    <div class="card-body position-relative z-2">
                                        <div class="d-inline-flex position-relative text-info p-3">
                                            <span class="position-absolute top-0 start-0 w-100 h-100 bg-white rounded-pill d-none-dark"></span>
                                            <span class="position-absolute top-0 start-0 w-100 h-100 bg-body-secondary rounded-pill d-none d-block-dark"></span>
                                            <i class="fi-percent position-relative z-2 fs-4 m-1"></i>
                                        </div>
                                        <h3 class="h6 pt-2 my-2">Povećana vidljivost u rezultatima pretraživanja</h3>
                                        <p class="fs-sm mb-0">Objavom logotipa  stječete vrijedne poveznice prema svojoj web stranici. To
                                            je čimbenik koji može doprinijeti poboljšanju vašeg položaja u rezultatima
                                            pretraživanja</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Preporuke -->
                            <div class="col">
                                <div class="card h-100 bg-transparent border-0">
                                    <span class="position-absolute top-0 start-0 w-100 h-100 bg-white border border-white border-opacity-75 rounded-4 d-none-dark" style="--fn-bg-opacity: .3"></span>
                                    <span class="position-absolute top-0 start-0 w-100 h-100 bg-white border rounded-4 d-none d-block-dark" style="--fn-bg-opacity: .05"></span>
                                    <div class="card-body position-relative z-2">
                                        <div class="d-inline-flex position-relative text-info p-3">
                                            <span class="position-absolute top-0 start-0 w-100 h-100 bg-white rounded-pill d-none-dark"></span>
                                            <span class="position-absolute top-0 start-0 w-100 h-100 bg-body-secondary rounded-pill d-none d-block-dark"></span>
                                            <i class="fi-heart position-relative z-2 fs-4 m-1"></i>
                                        </div>
                                        <h3 class="h6 pt-2 my-2">Proširite krug poslovnih partnera</h3>
                                        <p class="fs-sm mb-0">Proširite svoj broj  kontakata preporučujući poslovne partnere. Ova značajka
                                            povećava vašu vidljivost unutar poslovne zajednice.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Fleksibilno oglašavanje -->
                            <div class="col">
                                <div class="card h-100 bg-transparent border-0">
                                    <span class="position-absolute top-0 start-0 w-100 h-100 bg-white border border-white border-opacity-75 rounded-4 d-none-dark" style="--fn-bg-opacity: .3"></span>
                                    <span class="position-absolute top-0 start-0 w-100 h-100 bg-white border rounded-4 d-none d-block-dark" style="--fn-bg-opacity: .05"></span>
                                    <div class="card-body position-relative z-2">
                                        <div class="d-inline-flex position-relative text-info p-3">
                                            <span class="position-absolute top-0 start-0 w-100 h-100 bg-white rounded-pill d-none-dark"></span>
                                            <span class="position-absolute top-0 start-0 w-100 h-100 bg-body-secondary rounded-pill d-none d-block-dark"></span>
                                            <i class="fi-pie-chart position-relative z-2 fs-4 m-1"></i>
                                        </div>
                                        <h3 class="h6 pt-2 my-2">Prirodan rast prometa</h3>
                                        <p class="fs-sm mb-0">Objavljeni logo pridonosi privlačenju novih posjetitelja na vašu stranicu na
                                            prirodan način, potičući održivi rast.</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>






<!-- Vendor scripts -->
<script src="{{ asset('theme1/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

<!-- Bootstrap + Theme scripts -->
<script src="{{ asset('theme1/assets/theme.js') }}"></script>

@livewireScripts

</body>
</html>
