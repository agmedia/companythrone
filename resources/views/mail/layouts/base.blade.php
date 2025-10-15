<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
</head>
<body style="margin:0; padding:0; background-color:#f6f8fa; color:#1e293b; font-family:'Helvetica Neue', Helvetica, Arial, sans-serif; line-height:1.5;">

<div style="width:100%; background-color:#f6f8fa; padding:30px 0;">
    <div style="max-width:620px; margin:0 auto; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 14px rgba(0,0,0,0.06);">

        <!-- Header -->
        <div style="background-color:#c92d2d; color:#ffffff; text-align:center; padding:25px;">
            <a href="{{ config('app.url') }}" target="_blank" style="text-decoration:none; display:inline-block;">
                <img src="{{ asset('img/companythrone-round.png') }}" alt="{{ config('app.name') }}" style="max-height:50px; display:block; margin:0 auto;">
            </a>
        </div>

        <!-- Body -->
        <div style="padding:30px;">
            @yield('content')
        </div>

        <!-- Footer -->
        <div style="text-align:center; font-size:13px; color:#6b7280; padding:20px 30px 10px;">
            <p style="margin:0 0 5px;">&copy; {{ date('Y') }} {{ config('app.name') }}.<br>{{ __('Sva prava pridržana.') }}</p>
            <p style="margin:0;">
                <a href="{{ config('app.url') }}" style="color:#c92d2d; text-decoration:none;">{{ config('app.url') }}</a>
            </p>
        </div>

    </div>
</div>

</body>
</html>
