<div>
    <h1 class="h4 mb-2">{{ __('auth.confirm_password_title') }}</h1>
    <p class="text-muted small mb-3">{{ __('auth.confirm_password_text') }}</p>

    <form wire:submit.prevent="confirmPassword" class="vstack gap-3">
        <div>
            <label class="form-label" for="password">{{ __('auth.password') }}</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                   wire:model.defer="password" autocomplete="current-password" autofocus>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">{{ __('auth.confirm') }}</button>
    </form>

</div>