<aside class="col-lg-3">
    <div class="list-group mb-4">
        <a href="{{ route('account.dashboard') }}"
           class="list-group-item list-group-item-action {{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
            <i class="fi-home me-2"></i> {{ __('Dashboard') }}
        </a>
        <a href="{{ route('account.links.index') }}"
           class="list-group-item list-group-item-action {{ request()->routeIs('account.links.*') ? 'active' : '' }}">
            <i class="fi-link me-2"></i> {{ __('Moji linkovi') }}
        </a>
        <a href="{{ route('account.payments') }}"
           class="list-group-item list-group-item-action {{ request()->routeIs('account.payments') ? 'active' : '' }}">
            <i class="fi-credit-card me-2"></i> {{ __('Računi') }}
        </a>
        <a href="{{ route('account.subscriptions') }}"
           class="list-group-item list-group-item-action {{ request()->routeIs('account.subscriptions') ? 'active' : '' }}">
            <i class="fi-box me-2"></i> {{ __('Pretplate') }}
        </a>
        <a href="{{ route('account.profile.edit') }}"
           class="list-group-item list-group-item-action {{ request()->routeIs('account.profile.*') ? 'active' : '' }}">
            <i class="fi-user me-2"></i> {{ __('Moj profil') }}
        </a>
    </div>
</aside>
