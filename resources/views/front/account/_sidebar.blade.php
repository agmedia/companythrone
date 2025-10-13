<aside class="col-lg-3">
    <h1 class="h3 mb-4">{{ __('Moj račun') }}</h1>

    <div class="list-group mb-4">
        <a href="{{ route('account.dashboard') }}"
           class="list-group-item list-group-item-action {{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
            <i class="fi-home me-2"></i> {{ __('Dashboard') }}
        </a>
        @if (auth()->user()->hasRole('company_owner'))
            @if (auth()->user()->company && subscription_active(auth()->user()->company->id))
                <a href="{{ route('account.links.index') }}"
                   class="list-group-item list-group-item-action {{ request()->routeIs('account.links.*') ? 'active' : '' }}">
                    <i class="fi-link me-2"></i> {{ __('Moji linkovi') }}
                </a>
            @endif
            <a href="{{ route('account.subscriptions') }}"
               class="list-group-item list-group-item-action {{ request()->routeIs('account.subscriptions') ? 'active' : '' }}">
                <i class="fi-check-shield me-2"></i> {{ __('Pretplate') }}
            </a>
            <a href="{{ route('account.payments') }}"
               class="list-group-item list-group-item-action {{ request()->routeIs('account.payments') ? 'active' : '' }}">
                <i class="fi-credit-card me-2"></i> {{ __('Računi') }}
            </a>
        @endif
        <a href="{{ route('account.profile.edit') }}"
           class="list-group-item list-group-item-action {{ request()->routeIs('account.profile.*') ? 'active' : '' }}">
            <i class="fi-user me-2"></i> {{ __('Moj profil') }}
        </a>
        @if (auth()->user()->hasRole('company_owner'))
            <a href="{{ route('account.company.edit') }}"
               class="list-group-item list-group-item-action {{ request()->routeIs('account.company.*') ? 'active' : '' }}">
                <i class="fi-anchor me-2"></i> {{ __('Moja tvrtka') }}
            </a>
        @endif
    </div>
</aside>
