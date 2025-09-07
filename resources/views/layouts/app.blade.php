<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name','Companythrone'))</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @livewireStyles
</head>
<body class="bg-light">

{{-- Header jednom, globalno --}}
@include('components.layouts.app.header')

<div class="container-fluid">
    <div class="row">
        {{-- Sidebar (samo za prijavljene) --}}
        <aside class="col-lg-3 col-xl-2 border-end p-0">
            @auth
                @include('components.layouts.app.sidebar')
            @endauth
        </aside>

        {{-- Glavni sadržaj --}}
        <main class="col-lg-9 col-xl-10 py-4 px-4">
            @yield('content')
        </main>
    </div>
</div>

@livewireScripts
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
