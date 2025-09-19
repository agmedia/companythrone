<div>
    <h1 class="h2 mt-auto">{{ __('auth.register_title') }}</h1>

    <form wire:submit.prevent="register" class="vstack gap-3">
        <div>
            <label class="form-label" for="name">{{ __('auth.name') }}</label>
            <input id="name" type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                   wire:model.defer="name" autocomplete="name" autofocus>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="form-label" for="email">{{ __('auth.email') }}</label>
            <input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                   wire:model.defer="email" autocomplete="email">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="form-label" for="password">{{ __('auth.password') }}</label>
            <div class="password-toggle">
            <input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                   wire:model.defer="password" autocomplete="new-password">
                <label class="password-toggle-button fs-lg" aria-label="Show/hide password">
                    <input type="checkbox" class="btn-check">
                </label>
            </div>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="form-label" for="password_confirmation">{{ __('auth.password_confirm') }}</label>
            <div class="password-toggle">
            <input id="password_confirmation" type="password" class="form-control form-control-lg"
                   wire:model.defer="password_confirmation" autocomplete="new-password">
                <label class="password-toggle-button fs-lg" aria-label="Show/hide password">
                    <input type="checkbox" class="btn-check">
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-lg btn-primary w-100">{{ __('auth.create_account') }}</button>

        <div class="text-center small mt-2">
            {{ __('auth.have_account') }} <a href="{{ route('login') }}">{{ __('auth.login') }}</a>
        </div>
    </form>

</div>
