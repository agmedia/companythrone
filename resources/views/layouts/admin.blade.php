<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','Admin · Companythrone')</title>
    @livewireStyles
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Inter,Ubuntu,Helvetica,Arial,sans-serif}
        .container{max-width:1200px;margin:0 auto;padding:24px}
        nav a{margin-right:12px}
    </style>
</head>
<body>
<nav class="container">
    <a href="{{ route('admin.companies') }}">{{ __('company.admin_companies') }}</a>
    <a href="{{ route('admin.categories') }}">{{ __('company.admin_categories') }}</a>
</nav>
<main class="container">@yield('content')</main>
@livewireScripts
</body>
</html>
