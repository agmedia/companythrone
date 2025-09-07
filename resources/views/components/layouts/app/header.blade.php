<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container-xxl">
    <a class="navbar-brand fw-semibold" href="{{ localized_route('home') }}">Companythrone</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ localized_route('home') }}">{{ __('nav.home') }}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('companies.create') ? 'active' : '' }}" href="{{ localized_route('companies.create') }}">{{ __('nav.add_company') }}</a>
        </li>
        @auth
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
          </li>
        @endauth
      </ul>
      <ul class="navbar-nav align-items-center gap-2">
        @php
          $supported = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales();
          $current = app()->getLocale();
        @endphp
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-uppercase" href="#" role="button" data-bs-toggle="dropdown">
            {{ $current }}
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            @foreach($supported as $localeCode => $properties)
              <li>
                <a class="dropdown-item {{ $localeCode === $current ? 'active' : '' }}" rel="alternate" hreflang="{{ $localeCode }}" href="{{ Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                  {{ $properties['name'] ?? strtoupper($localeCode) }}
                </a>
              </li>
            @endforeach
          </ul>
        </li>
        @auth
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
              <img src="{{ asset(auth()->user()->detail->avatar ?? 'media/avatars/default_avatar.png') }}" alt="avatar" class="rounded-circle" width="28" height="28">
              <span class="d-none d-sm-inline">{{ \Illuminate\Support\Str::of(auth()->user()->name ?? 'User')->limit(18) }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="{{ route('settings.profile') }}">@lang('settings.profile_title')</a></li>
              <li><a class="dropdown-item" href="{{ route('settings.password') }}">@lang('settings.password_title')</a></li>
              <li><a class="dropdown-item" href="{{ route('settings.appearance') }}">@lang('settings.appearance_title')</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('logout') }}" class="px-3">
                  @csrf
                  <button class="btn btn-link p-0 text-danger">{{ __('auth.logout') }}</button>
                </form>
              </li>
            </ul>
          </li>
        @else
          <li class="nav-item"><a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">{{ __('auth.login') }}</a></li>
        @endauth
      </ul>
    </div>
  </div>
</nav>
