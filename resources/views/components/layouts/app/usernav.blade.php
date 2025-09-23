<div class="offcanvas-body d-block pt-2 pt-lg-4 pb-lg-0">
    <nav class="list-group list-group-borderless">

        <a class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           href="{{ route('dashboard') }}"
           @if(request()->routeIs('dashboard')) aria-current="page" @endif>
            <i class="fi-user fs-base opacity-75 me-2"></i>
            Korisnička ploča
        </a>

        <a class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('settings.profile') ? 'active' : '' }}"
           href="{{ route('settings.profile') }}"
           @if(request()->routeIs('settings.profile')) aria-current="page" @endif>
            <i class="fi-settings fs-base opacity-75 me-2"></i>
            Moji podaci
        </a>

        <a class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('settings.password') ? 'active' : '' }}"
           href="{{ route('settings.password') }}"
           @if(request()->routeIs('settings.password')) aria-current="page" @endif>
            <i class="fi-shield fs-base opacity-75 me-2"></i>
            Lozinka
        </a>

        <a class="list-group-item list-group-item-action d-flex align-items-center {{ request()->routeIs('settings.company') ? 'active' : '' }}"
           href="{{ route('settings.company') }}"
           @if(request()->routeIs('settings.company')) aria-current="page" @endif>
            <i class="fi-layers fs-base opacity-75 me-2"></i>
            Moja tvrtka
        </a>

    </nav>

    <nav class="list-group list-group-borderless pt-3">
        <form method="POST" action="{{ route('logout') }}" class="px-0">
            @csrf
            <button class="list-group-item list-group-item-action d-flex align-items-center">
                <i class="fi-log-out fs-base opacity-75 me-2"></i>
                {{ __('auth.logout') }}
            </button>
        </form>
    </nav>
</div>

