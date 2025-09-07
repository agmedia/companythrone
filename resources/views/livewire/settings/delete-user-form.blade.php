<section class="mt-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h2 class="h6 text-danger mb-2">{{ __('settings.delete_account_title') }}</h2>
            <p class="text-muted small mb-3">{{ __('settings.delete_account_subtitle') }}</p>

            <form wire:submit.prevent="deleteUser" class="vstack gap-3">
                <div>
                    <label class="form-label" for="password">{{ __('auth.password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                           wire:model.defer="password" autocomplete="current-password">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-danger">{{ __('settings.delete_account_button') }}</button>
            </form>
        </div>
    </div>
</section>
