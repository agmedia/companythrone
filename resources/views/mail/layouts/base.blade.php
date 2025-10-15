<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <style>
        :root {
            --primary: #2563eb;
            --gray: #f6f8fa;
            --text: #1e293b;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--gray);
            color: var(--text);
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            line-height: 1.5;
        }

        a {
            color: var(--primary);
            text-decoration: none;
        }

        .email-container {
            width: 100%;
            background-color: var(--gray);
            padding: 30px 0;
        }

        .email-wrapper {
            background-color: #ffffff;
            max-width: 620px;
            margin: 0 auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
        }

        .email-header {
            background-color: var(--primary);
            color: #fff;
            text-align: center;
            padding: 25px;
        }

        .email-header img {
            max-height: 50px;
        }

        .email-body {
            padding: 30px;
        }

        .email-footer {
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            padding: 20px 30px 10px;
        }

        .btn {
            display: inline-block;
            background-color: var(--primary);
            color: #fff;
            text-decoration: none;
            padding: 10px 22px;
            border-radius: 6px;
            font-weight: 600;
        }

        @media (max-width: 600px) {
            .email-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-wrapper">
        {{-- Header --}}
        <div class="email-header">
            <a href="{{ config('app.url') }}" target="_blank">
                <img src="{{ asset('images/logo-mail.png') }}" alt="{{ config('app.name') }}">
            </a>
        </div>

        {{-- Content --}}
        <div class="email-body">
            @yield('content')
        </div>

        {{-- Footer --}}
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}.<br>
                {{ __('Sva prava pridržana.') }}</p>
            <p>
                <a href="{{ config('app.url') }}">{{ config('app.url') }}</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>
