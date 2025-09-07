<div>
    <h1 class="h4 mb-2">{{ __('auth.forgot_password_title') }}</h1>
    <p class="text-muted small mb-3">{{ __('auth.forgot_password_hint') }}</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form wire:submit.prevent="sendPasswordResetLink" class="vstack gap-3">
        <div>
            <label class="form-label" for="email">{{ __('auth.email') }}</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                   wire:model.defer="email" autocomplete="email" autofocus>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">{{ __('auth.send_reset_link') }}</button>

        <div class="text-center small mt-2">
            <a href="{{ route('login') }}">{{ __('auth.back_to_login') }}</a>
        </div>
    </form>

</div>