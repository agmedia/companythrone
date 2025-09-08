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

    <!-- Theme switcher (color modes) -->
    <script src="{{ asset('theme1/assets/js/theme-switcher.js') }}"></script>

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

<body class="bg-light">

{{-- Header jednom, globalno --}}
@include('components.layouts.app.header')

<!-- Page content -->
<main class="content-wrapper">

    @yield('content')

</main>

@include('components.layouts.app.footer')

<!-- Back to top button -->
<div class="floating-buttons position-fixed top-50 end-0 z-sticky me-3 me-xl-4 pb-4">
    <a class="btn-scroll-top btn btn-sm bg-body border-0 rounded-pill shadow animate-slide-end" href="#top">
        Top
        <i class="fi-arrow-right fs-base ms-1 me-n1 animate-target"></i>
        <span class="position-absolute top-0 start-0 w-100 h-100 border rounded-pill z-0"></span>
        <svg class="position-absolute top-0 start-0 w-100 h-100 z-1" viewBox="0 0 62 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x=".75" y=".75" width="60.5" height="30.5" rx="15.25" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10"/>
        </svg>
    </a>
</div>

<!-- Vendor scripts -->
<script src="{{ asset('theme1/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

<!-- Bootstrap + Theme scripts -->
<script src="{{ asset('theme1/assets/theme.js') }}"></script>

@livewireScripts

</body>
</html>
