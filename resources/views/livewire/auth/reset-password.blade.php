<div>
    <h1 class="h4 mb-3">{{ __('auth.reset_password_title') }}</h1>

    <form wire:submit.prevent="resetPassword" class="vstack gap-3">
        <div>
            <label class="form-label" for="email">{{ __('auth.email') }}</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                   wire:model.defer="email" autocomplete="email" autofocus>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="form-label" for="password">{{ __('auth.new_password') }}</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                   wire:model.defer="password" autocomplete="new-password">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="form-label" for="password_confirmation">{{ __('auth.password_confirm') }}</label>
            <input id="password_confirmation" type="password" class="form-control"
                   wire:model.defer="password_confirmation" autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary w-100">{{ __('auth.reset_password') }}</button>
    </form>

</div>