<!-- [ Sidebar Menu ] start -->
@php
    $is = fn($pattern) => request()->routeIs($pattern);
    $catalogOpen = $is('catalog.*');              // open Catalog when on any catalog.* route
    $currentGroup = request('group', 'companies'); // default group for categories link
@endphp

<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <img src="{{ asset('admin/theme1/assets/images/logo-dark.svg') }}" class="img-fluid logo-lg" alt="logo" />
            </a>
        </div>
        <div class="navbar-content">
            <div class="card pc-user-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('admin/theme1/assets/images/user/avatar-1.jpg') }}" alt="user-image" class="user-avtar wid-45 rounded-circle" />
                        </div>
                        <div class="flex-grow-1 ms-3 me-2">
                            <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                            <small>{{ __('back/common.roles.administrator') }}</small>
                        </div>
                        <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse" href="#pc_sidebar_userlink" aria-expanded="false" aria-controls="pc_sidebar_userlink">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-sort-outline"></use>
                            </svg>
                        </a>
                    </div>
                    <div class="collapse pc-user-links" id="pc_sidebar_userlink">
                        <div class="pt-3">
                            {{--<a href="{{ route('users.profile') }}">
                                <i class="ti ti-user"></i>
                                <span>@lang('back/common.roles.administrator')</span>
                            </a>--}}
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="ti ti-power"></i>
                                <span>{{ __('back/nav.user.logout') }}</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="pc-navbar">
                <li class="pc-item {{ $is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="pc-link">
                        <span class="pc-micon">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-element-plus"></use>
                            </svg>
                        </span>
                        <span class="pc-mtext">{{ __('back/nav.dashboard') }}</span>
                    </a>
                </li>

                <!-- Catalog -->
                <li class="pc-item pc-hasmenu {{ $catalogOpen ? 'active pc-trigger' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-layer"></use></svg></span>
                        <span class="pc-mtext" data-i18n="Dashboard">{{ __('back/nav.catalog') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ $is('categories.*') ? 'active' : '' }}">
                            <a class="pc-link"
                               href="{{ route('catalog.categories.index', ['group' => $currentGroup]) }}"
                               data-i18n="Categories">
                                {{ __('back/nav.categories') }}
                            </a>
                        </li>
                        <li class="pc-item {{ $is('companies.*') ? 'active' : '' }}">
                            <a class="pc-link"
                               href="{{ route('catalog.companies.index') }}"
                               data-i18n="Categories">
                                {{ __('back/nav.companies') }}
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="pc-item">
                    <a href="{{ route('subscriptions.index') }}" class="pc-link">
                        <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-fatrows"></use></svg></span>
                        <span class="pc-mtext">{{ __('back/nav.orders') }}</span>
                    </a>
                </li>

                <li class="pc-item">
                    <a href="{{ route('banners.index') }}" class="pc-link">
                        <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-video-play"></use></svg></span>
                        <span class="pc-mtext">{{ __('back/nav.banners') }}</span>
                    </a>
                </li>

                {{--<li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-status-up"></use></svg></span>
                        <span class="pc-mtext" data-i18n="Dashboard">{{ __('back/nav.marketing') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a class="pc-link" href="../dashboard/index.html" data-i18n="Default">{{ __('back/nav.actions') }}</a></li>
                        <li class="pc-item"><a class="pc-link" href="../dashboard/index.html" data-i18n="Default">{{ __('back/nav.actions') }}</a></li>
                    </ul>
                </li>--}}

                {{--<li class="pc-item {{ $is('users.*') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="pc-link">
                        <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-profile-2user-outline"></use></svg></span>
                        <span class="pc-mtext">{{ __('back/nav.users') }}</span>
                    </a>
                </li>--}}

                <!-- Settings -->
                <li class="pc-item pc-caption">
                    <label>{{ __('back/nav.settings_caption') }}</label>
                    <svg class="pc-icon">
                        <use xlink:href="#custom-setting-2"></use>
                    </svg>
                </li>

                <li class="pc-item pc-hasmenu {{ request()->routeIs('app.settings.*') ? 'pc-trigger active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-setting-2"></use></svg></span>
                        <span class="pc-mtext" data-i18n="Dashboard">{{ __('back/nav.settings') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item {{ request()->routeIs('app.settings.*') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('app.settings.index') }}" data-i18n="Default">{{ __('back/nav.application') }}</a>
                        </li>
                    </ul>
                </li>

                <li class="pc-item pc-hasmenu {{ request()->routeIs('settings.*') ? 'pc-trigger active' : '' }}">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"><svg class="pc-icon"><use xlink:href="#custom-presentation-chart"></use></svg></span>
                        <span class="pc-mtext">{{ __('back/nav.shop') }}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>

                    <ul class="pc-submenu">
                        <li class="pc-item {{ request()->routeIs('settings.geozones.*') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('settings.geozones.index') }}">
                                {{ __('back/nav.geozones') }}
                            </a>
                        </li>

                        <li class="pc-item {{ request()->routeIs('settings.currencies.*') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('settings.currencies.index') }}">
                                {{ __('back/nav.currencies') }}
                            </a>
                        </li>

                        <li class="pc-item {{ request()->routeIs('settings.languages.*') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('settings.languages.index') }}">
                                {{ __('back/nav.languages') }}
                            </a>
                        </li>

                        <li class="pc-item {{ request()->routeIs('settings.taxes.*') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('settings.taxes.index') }}">
                                {{ __('back/nav.taxes') }}
                            </a>
                        </li>

                        <li class="pc-item {{ request()->routeIs('settings.statuses.*') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('settings.statuses.index') }}">
                                {{ __('back/nav.order_statuses') }}
                            </a>
                        </li>

                        <li class="pc-item {{ request()->routeIs('settings.payments.*') ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('settings.payments.index') }}">
                                {{ __('back/nav.payments') }}
                            </a>
                        </li>

                    </ul>
                </li>


            </ul>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->
