<div>
    <h1 class="h4 mb-3">{{ __('auth.register_title') }}</h1>

    <form wire:submit.prevent="register" class="vstack gap-3">
        <div>
            <label class="form-label" for="name">{{ __('auth.name') }}</label>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                   wire:model.defer="name" autocomplete="name" autofocus>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="form-label" for="email">{{ __('auth.email') }}</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                   wire:model.defer="email" autocomplete="email">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="form-label" for="password">{{ __('auth.password') }}</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                   wire:model.defer="password" autocomplete="new-password">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="form-label" for="password_confirmation">{{ __('auth.password_confirm') }}</label>
            <input id="password_confirmation" type="password" class="form-control"
                   wire:model.defer="password_confirmation" autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary w-100">{{ __('auth.create_account') }}</button>

        <div class="text-center small mt-2">
            {{ __('auth.have_account') }} <a href="{{ route('login') }}">{{ __('auth.login') }}</a>
        </div>
    </form>

</div>