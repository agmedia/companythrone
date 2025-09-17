<!-- Navigation bar (Page header) -->
<header class="navbar navbar-expand-lg bg-body navbar-sticky sticky-top z-fixed px-0" data-sticky-element>
    <div class="container">

        <!-- Mobile offcanvas menu toggler (Hamburger) -->
        <button type="button" class="navbar-toggler me-3 me-lg-0" data-bs-toggle="offcanvas" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar brand (Logo) -->
        <a class="navbar-brand py-1 py-md-2 py-xl-1 me-2 me-sm-n4 me-md-n5 me-lg-0" href="{{ localized_route('home') }}">
          <span class=" d-flex flex-shrink-0 text-primary rtl-flip me-2">
          <img src="{{ asset('theme1/assets/companythrone-round.svg') }}" alt="Companythrone">
          </span>
            <span class="d-none d-sm-flex ">
            {{ config('app.name','Companythrone') }}
            </span>
        </a>

        <!-- Main navigation that turns into offcanvas on screens < 992px wide (lg breakpoint) -->
        <nav class="offcanvas offcanvas-start" id="navbarNav" tabindex="-1" aria-labelledby="navbarNavLabel">
            <div class="offcanvas-header py-3">
                <h5 class="offcanvas-title" id="navbarNavLabel">{{ config('app.name','Companythrone') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body pt-2 pb-4 py-lg-0 mx-lg-auto">
                <ul class="navbar-nav position-relative">
                    <li class="nav-item py-lg-2 me-lg-n2 me-xl-0">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ localized_route('home') }}">{{ __('nav.home') }}</a>
                    </li>
                    <li class="nav-item py-lg-2 me-lg-n2 me-xl-0">
                        <a class="nav-link {{ request()->routeIs('companies.index') ? 'active' : '' }}" href="{{ localized_route('companies.index') }}">{{ __('nav.companies') }}</a>
                    </li>
                   {{--  <li class="nav-item py-lg-2 me-lg-n2 me-xl-0">
                        <a class="nav-link {{ request()->routeIs('companies.create') ? 'active' : '' }}" href="{{ localized_route('companies.create') }}">{{ __('nav.add_company') }}</a>
                    </li>--}}
                    @auth
                        <li class="nav-item py-lg-2 me-lg-n2 me-xl-0">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                    @endauth
                    {{--<li class="nav-item dropdown py-lg-2 me-lg-n1 me-xl-0">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" data-bs-trigger="hover" data-bs-auto-close="outside" aria-expanded="false">Account</a>
                        <ul class="dropdown-menu">
                            <li class="dropend">
                                <a class="dropdown-item dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" data-bs-trigger="hover" aria-expanded="false">Auth Pages</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="account-signin.html">Sign In</a></li>
                                    <li><a class="dropdown-item" href="account-signup.html">Sign Up</a></li>
                                    <li><a class="dropdown-item" href="account-password-recovery.html">Password Recovery</a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="account-profile.html">My Profile</a></li>
                            <li><a class="dropdown-item" href="account-listings.html">My Listings</a></li>
                            <li><a class="dropdown-item" href="account-reviews.html">Reviews</a></li>
                            <li><a class="dropdown-item" href="account-favorites.html">Favorites</a></li>
                            <li><a class="dropdown-item" href="account-payment.html">Payment Details</a></li>
                            <li><a class="dropdown-item" href="account-settings.html">Account Settings</a></li>
                        </ul>
                    </li>--}}
                </ul>
            </div>
        </nav>

        <!-- Button group -->
        <div class="d-flex gap-sm-1">


            <!-- Language switcher (hr/en/...) -->
            @php
                $supported = Mcamara\LaravelLocalization\Facades\LaravelLocalization::getSupportedLocales();
                $current = app()->getLocale();
            @endphp
            <div class="dropdown">
                <button type="button" class="theme-switcher btn btn-icon btn-outline-secondary fs-lg border-0 animate-scale" data-bs-toggle="dropdown" data-bs-display="dynamic" aria-expanded="false" aria-label="Toggle language">
                    <span class="theme-icon-active d-flex animate-target"> {{ $current }}
                </button>
                <ul class="dropdown-menu start-50 translate-middle-x" style="--fn-dropdown-min-width: 9rem; --fn-dropdown-spacer: .5rem">
                    @foreach($supported as $localeCode => $properties)
                        <li>
                            <a class="dropdown-item {{ $localeCode === $current ? 'active' : '' }}" data-bs-theme-value="light" aria-pressed="true" hreflang="{{ $localeCode }}" href="{{ Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                {{ $properties['name'] ?? strtoupper($localeCode) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- User (login/profile/logout/...) -->
            @auth
                <div class="dropdown">
                    <button type="button" class="theme-switcher btn btn-icon btn-outline-secondary fs-lg border-0 animate-scale" data-bs-toggle="dropdown" data-bs-display="dynamic" aria-expanded="false" aria-label="Toggle user account">
                        <span class="theme-icon-active d-flex animate-target"><i class="fi-user-check"></i></span>
                    </button>
                    <ul class="dropdown-menu start-50 translate-middle-x" style="--fn-dropdown-min-width: 9rem; --fn-dropdown-spacer: .5rem">
                        <li><a class="dropdown-item" href="{{ route('settings.profile') }}">@lang('settings.profile_title')</a></li>
                        <li><a class="dropdown-item" href="{{ route('settings.password') }}">@lang('settings.password_title')</a></li>
                        {{--<li><a class="dropdown-item" href="{{ route('settings.appearance') }}">@lang('settings.appearance_title')</a></li>--}}
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="px-3">
                                @csrf
                                <button class="btn btn-link p-0 text-danger">{{ __('auth.logout') }}</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a class="btn btn-icon btn-outline-secondary fs-lg border-0 animate-shake me-2" href="{{ route('login') }}" aria-label="{{ __('auth.login') }}">
                    <i class="fi-user animate-target"></i>
                </a>
            @endauth

            <!-- Join button  -->
            <a class="btn btn-primary animate-scale" href="{{ localized_route('companies.create') }}">
                <i class="fi-plus fs-lg animate-target ms-n2 me-1 me-sm-2"></i> {{ __('nav.add_company') }} </span>
            </a>
        </div>
    </div>
</header>
