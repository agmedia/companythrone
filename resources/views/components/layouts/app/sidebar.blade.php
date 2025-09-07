{{-- Offcanvas for mobile, visible as sidebar on lg+ --}}
<div class="offcanvas offcanvas-start offcanvas-lg bg-light" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header d-lg-none">
        <h5 class="offcanvas-title" id="sidebarLabel">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0">
        <div class="d-flex flex-column flex-grow-1">
            <div class="px-3 py-3 border-bottom d-none d-lg-block">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset(auth()->user()->detail->avatar ?? 'media/avatars/default_avatar.png') }}" class="rounded-circle" width="40" height="40" alt="avatar">
                    <div class="small">
                        <div class="fw-semibold">{{ auth()->user()->name ?? 'User' }}</div>
                        <div class="text-muted">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                </div>
            </div>

            <nav class="nav flex-column py-2">
                <a class="nav-link px-3 {{ request()->routeIs('dashboard') ? 'active fw-semibold' : 'text-body' }}"
                   href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a class="nav-link px-3 {{ request()->routeIs('home') ? 'active fw-semibold' : 'text-body' }}"
                   href="{{ localized_route('home') }}">
                    <i class="bi bi-house-door me-2"></i> {{ __('nav.home') }}
                </a>
                <a class="nav-link px-3 {{ request()->routeIs('companies.create') ? 'active fw-semibold' : 'text-body' }}"
                   href="{{ localized_route('companies.create') }}">
                    <i class="bi bi-building-add me-2"></i> {{ __('nav.add_company') }}
                </a>

                @hasanyrole('master|admin')
                <div class="px-3 pt-3 pb-1 text-uppercase small text-muted">Admin</div>
                <a class="nav-link px-3 {{ request()->routeIs('admin.companies') ? 'active fw-semibold' : 'text-body' }}"
                   href="{{ route('admin.companies') }}">
                    <i class="bi bi-buildings me-2"></i> Tvrtke
                </a>
                <a class="nav-link px-3 {{ request()->routeIs('admin.categories') ? 'active fw-semibold' : 'text-body' }}"
                   href="{{ route('admin.categories') }}">
                    <i class="bi bi-diagram-3 me-2"></i> Kategorije
                </a>
                @endhasanyrole

                <div class="px-3 pt-3 pb-1 text-uppercase small text-muted">Postavke</div>
                <a class="nav-link px-3 {{ request()->routeIs('settings.profile') ? 'active fw-semibold' : 'text-body' }}"
                   href="{{ route('settings.profile') }}">
                    <i class="bi bi-person me-2"></i> {{ __('settings.profile_title') }}
                </a>
                <a class="nav-link px-3 {{ request()->routeIs('settings.password') ? 'active fw-semibold' : 'text-body' }}"
                   href="{{ route('settings.password') }}">
                    <i class="bi bi-key me-2"></i> {{ __('settings.password_title') }}
                </a>
                <a class="nav-link px-3 {{ request()->routeIs('settings.appearance') ? 'active fw-semibold' : 'text-body' }}"
                   href="{{ route('settings.appearance') }}">
                    <i class="bi bi-palette me-2"></i> {{ __('settings.appearance_title') }}
                </a>
            </nav>

            <div class="mt-auto border-top p-3">
                <form method="POST" action="{{ route('logout') }}" class="d-grid">
                    @csrf
                    <button class="btn btn-outline-danger">{{ __('Log out') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
