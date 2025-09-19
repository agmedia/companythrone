

<div>



    <h1 class="h2 mt-auto">{{ __('auth.login_title') }}</h1>


    <div class="nav fs-sm mb-4">
        {{ __('auth.no_account') }}
        <a class="nav-link text-decoration-underline p-0 ms-2" href="{{ route('register') }}">{{ __('auth.register') }}</a>
    </div>


    <form wire:submit.prevent="login" class="vstack gap-3">
        <div>
            <label class="form-label" for="email">{{ __('auth.email') }}</label>
            <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                   wire:model.defer="email" autocomplete="email" autofocus>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>



        <div>
            <label class="form-label" for="password">{{ __('auth.password') }}</label>
            <div class="password-toggle">

            <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                   wire:model.defer="password" autocomplete="current-password">
                <label class="password-toggle-button fs-lg" aria-label="Show/hide password">
                    <input type="checkbox" class="btn-check">
                </label>
            </div>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" wire:model="remember">
                <label class="form-check-label" for="remember">{{ __('auth.remember') }}</label>
            </div>
            <a class="small" href="{{ route('password.request') }}">{{ __('auth.forgot_password') }}</a>
        </div>

        <button type="submit" class="btn btn-lg btn-primary w-100">{{ __('auth.login') }}</button>

        <div class="text-center small mt-2">
            {{ __('auth.no_account') }} <a href="{{ route('register') }}">{{ __('auth.register') }}</a>
        </div>
    </form>

</div>
