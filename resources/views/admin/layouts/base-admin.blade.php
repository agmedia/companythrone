<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>Companythrone - @yield('title', 'Admin Panel')</title>
    <!-- Meta -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon & Manifest -->
    <link rel="icon" href="{{ asset('admin/theme1/assets/images/favicon.svg') }}" type="image/x-icon" />

    <!-- Font Family -->
    <link rel="stylesheet" href="{{ asset('admin/theme1/assets/fonts/inter/inter.css') }}" id="main-font-link" />
    <!-- phosphor Icons -->
    <link rel="stylesheet" href="{{ asset('admin/theme1/assets/fonts/phosphor/duotone/style.css') }}" />
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="{{ asset('admin/theme1/assets/fonts/tabler-icons.min.css') }}" />
    <!-- Feather Icons -->
    <link rel="stylesheet" href="{{ asset('admin/theme1/assets/fonts/feather.css') }}" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('admin/theme1/assets/fonts/fontawesome.css') }}" />
    <!-- Material Icons -->
    <link rel="stylesheet" href="{{ asset('admin/theme1/assets/fonts/material.css') }}" />

    <!-- Template CSS Files -->
    <link rel="stylesheet" href="{{ asset('admin/theme1/assets/css/style.css') }}" id="main-style-link" />
    <link rel="stylesheet" href="{{ asset('admin/theme1/assets/css/style-preset.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin/theme1/assets/css/plugins/sweetalert2.css') }}" />








    @livewireStyles

    <!-- Additional CSS -->
    @stack('styles')
</head>

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">

<!-- [ Pre-loader ] start -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<!-- [ Pre-loader ] End -->


@include('admin.layouts.sidebar')

@include('admin.layouts.header')

<!-- [ Main Content ] start -->
<div class="pc-container">
    <div class="pc-content">
        @yield('content')
    </div>
</div>
<!-- [ Main Content ] end -->

@include('admin.layouts.footer')

{{--@include('admin.layouts.offcanvas')--}}

@stack('modals')

<!-- Required Js -->


<script src="{{ asset('admin/theme1/assets/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('admin/theme1/assets/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('admin/theme1/assets/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('admin/theme1/assets/js/icon/custom-font.js') }}"></script>
<script src="{{ asset('admin/theme1/assets/js/script.js') }}"></script>
<script src="{{ asset('admin/theme1/assets/js/theme.js') }}"></script>
<script src="{{ asset('admin/theme1/assets/js/plugins/feather.min.js') }}"></script>
<script src="{{ asset('admin/theme1/assets/js/plugins/choices.min.js') }}"></script>
<script src="{{ asset('admin/theme1/assets/js/plugins/sweetalert2.js') }}"></script>
<script src="{{ asset('admin/theme1/assets/js/plugins/axios.js') }}"></script>




@livewireScripts

<script>
    // Inicijalizacija SweetAlert mixina – pokreće se kad je Swal dostupan
    function initSwalMixins() {
        if (!window.Swal) return; // zaštita ako još nije učitan

        // učini globalnim za sve (i injected skripte)
        window.Swal = window.Swal || Swal;

        // napravi/obnovi mixin-e kao globalne reference
        window.confirmPopUp = Swal.mixin({
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success m-5',
                cancelButton: 'btn btn-danger m-5',
                input: 'form-control'
            }
        });

        window.successToast = Swal.mixin({
            position: 'top-end',
            icon: 'success',
            width: 270,
            showConfirmButton: false,
            timer: 1500,
            toast: true
        });

        window.errorToast = Swal.mixin({
            icon: 'error',            // ⚠️ v11: koristi 'icon', ne 'type'
            timer: 3000,
            position: 'top-end',
            showConfirmButton: false,
            toast: true
        });
    }

    // Pokreni nakon inicijalnog load-a
    document.addEventListener('DOMContentLoaded', initSwalMixins);

    // Livewire hook kad je sve registrirano
    document.addEventListener('livewire:load', function () {
        window.Swal = window.Swal || Swal;
        initSwalMixins();
    });

    // Vrlo bitno: nakon Livewire navigacije/partial zamjena DOM-a,
    // injected skripte ponekad odmah pozivaju Swal – zato opet inicijaliziraj.
    document.addEventListener('livewire:navigated', initSwalMixins);
</script>



{{-- Ostale tvoje skripte koje KORISTE Swal smjesti IZA ovoga
     ili provjeri postojanje: window.Swal && Swal.fire({...})
--}}

@stack('scripts')
