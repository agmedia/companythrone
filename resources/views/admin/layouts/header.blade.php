<!-- [ Header Topbar ] start -->
<header class="pc-header">
    <div class="header-wrapper">
        <!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item d-none d-md-inline-flex">
                    <form class="form-search">
                        <i class="search-icon">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-search-normal-1"></use>
                            </svg>
                        </i>
                        <input type="search" class="form-control" placeholder="Search..." />
                    </form>
                </li>
            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <svg class="pc-icon">
                            <use xlink:href="#custom-sun-1"></use>
                        </svg>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                        <a href="#!" class="dropdown-item" onclick="layout_change('dark')">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-moon"></use>
                            </svg>
                            <span>Dark</span>
                        </a>
                        <a href="#!" class="dropdown-item" onclick="layout_change('light')">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-sun-1"></use>
                            </svg>
                            <span>Light</span>
                        </a>
                    </div>
                </li>
                {{--<li class="dropdown pc-h-item">
                    <form id="locale-form" class="d-none" method="POST" action="--}}{{--{{ route('locale.switch') }}--}}{{--">
                        @csrf
                        <input type="hidden" name="locale" id="locale-input">
                    </form>
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        --}}{{--<i class="ti ti-language"></i>
                        <span class="d-none d-sm-inline">{{ strtoupper(shop()->getLocale()) }}</span>--}}{{--
                        <svg class="pc-icon">
                            <use xlink:href="#custom-language"></use>
                        </svg>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                        <a href="#" class="dropdown-item" onclick="event.preventDefault(); changeLocale('en')">
                            <span class="me-2">🇬🇧</span> @lang('back/common.language.english')
                        </a>
                        <a href="#" class="dropdown-item" onclick="event.preventDefault(); changeLocale('hr')">
                            <span class="me-2">🇭🇷</span> @lang('back/common.language.croatian')
                        </a>
                    </div>
                </li>--}}

                <li class="dropdown pc-h-item header-user-profile">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                        <img src="{{ asset('admin/theme1/assets/images/user/avatar-2.jpg') }}" alt="user-image" class="user-avtar" />
                    </a>
                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header d-flex align-items-center justify-content-between">
                            <h5 class="m-0">Profile</h5>
                        </div>
                        <div class="dropdown-body">
                            <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                                <div class="d-flex mb-1">
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('admin/theme1/assets/images/user/avatar-2.jpg') }}" alt="user-image" class="user-avtar wid-35" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ Auth::user()->name }}</h6>
                                        <span>{{ Auth::user()->email }}</span>
                                    </div>
                                </div>
                                <hr class="border-secondary border-opacity-50" />
                                <p class="text-span">Manage</p>
                                <a href="{{ route('settings.profile') }}" class="dropdown-item">
                                        <span>
                                            <svg class="pc-icon text-muted me-2">
                                                <use xlink:href="#custom-user"></use>
                                            </svg>
                                            <span>@lang('back/common.roles.administrator')</span>
                                        </span>
                                </a>
                                {{-- Link na front --}}
                                <a href="{{ url('/') }}" target="_blank" rel="noopener" class="dropdown-item">
                                    <span>
                                        <svg class="pc-icon text-muted me-2"><use xlink:href="#custom-external-link"></use></svg>
                                        <span>Visit front</span>
                                    </span>
                                </a>

                                {{-- Clear cache --}}
                                <a href="#" class="dropdown-item"
                                   onclick="event.preventDefault(); document.getElementById('clear-cache-form').submit();">
                                        <span>
                                            <svg class="pc-icon text-muted me-2"><use xlink:href="#custom-reload"></use></svg>
                                            <span>Clear caches</span>
                                        </span>
                                </a>
                                <form id="clear-cache-form" action="{{ route('tools.cache.clear') }}" method="POST" class="d-none">
                                    @csrf
                                </form>

                                {{-- Maintenance ON/OFF --}}
                                @php $isDown = app()->isDownForMaintenance(); @endphp

                                @if(!$isDown)
                                    <a href="#" class="dropdown-item text-warning"
                                       onclick="event.preventDefault(); if(confirm('Enable maintenance mode?')) document.getElementById('maintenance-on-form').submit();">
                                            <span>
                                                <svg class="pc-icon text-muted me-2"><use xlink:href="#custom-alert-triangle"></use></svg>
                                                <span>Maintenance ON</span>
                                            </span>
                                    </a>
                                    <form id="maintenance-on-form" action="{{ route('tools.maintenance.on') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                @else
                                    <a href="#" class="dropdown-item text-success"
                                       onclick="event.preventDefault(); document.getElementById('maintenance-off-form').submit();">
                                            <span>
                                                <svg class="pc-icon text-muted me-2"><use xlink:href="#custom-shield-check"></use></svg>
                                                <span>Maintenance OFF</span>
                                            </span>
                                    </a>
                                    <form id="maintenance-off-form" action="{{ route('tools.maintenance.off') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>

                                    @if(\Illuminate\Support\Facades\Cache::has('maintenance:secret'))
                                        <div class="px-3 py-2">
                                            <small class="text-muted">Bypass:</small>
                                            <div class="small">
                                                <a href="{{ url(Cache::get('maintenance:secret')) }}" target="_blank" rel="noopener">
                                                    {{ url(Cache::get('maintenance:secret')) }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <hr class="border-secondary border-opacity-50" />
                                <div class="d-grid">
                                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-primary">
                                        <svg class="pc-icon me-2">
                                            <use xlink:href="#custom-logout-1-outline"></use>
                                        </svg>
                                        Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
<!-- [ Header ] end -->

@push('scripts')
    <script>
        function changeLocale(lc) {
            document.getElementById('locale-input').value = lc;
            document.getElementById('locale-form').submit();
        }
    </script>
@endpush