<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name','Companythrone') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @livewireStyles
</head>
<body class="bg-light">
<div class="min-vh-100 d-flex flex-column justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-10 col-md-8 col-lg-5">
                <div class="text-center mb-3">
                    <a href="{{ localized_route('home') }}" class="fw-bold fs-4 text-decoration-none">Companythrone</a>
                </div>
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        {{ $slot }}
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a class="small text-decoration-none" href="{{ localized_route('home') }}">{{ __('nav.home') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@livewireScripts
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
