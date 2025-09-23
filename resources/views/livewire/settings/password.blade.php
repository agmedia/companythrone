<section class="w-100">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h2 class="h5 mb-1">{{ __('settings.password_title') }}</h2>
            <p class="text-muted small mb-3">{{ __('settings.password_subtitle') }}</p>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form wire:submit.prevent="updatePassword" class="vstack gap-3">
                <div>
                    <label class="form-label" for="current_password">{{ __('settings.current_password') }}</label>
                    <input id="current_password" type="password" class="form-control form-control-lg @error('current_password') is-invalid @enderror"
                           wire:model.defer="current_password" autocomplete="current-password">
                    @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="form-label" for="password">{{ __('auth.new_password') }}</label>
                    <input id="password" type="password" class="form-control  form-control-lg @error('password') is-invalid @enderror"
                           wire:model.defer="password" autocomplete="new-password">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="form-label" for="password_confirmation">{{ __('auth.password_confirm') }}</label>
                    <input id="password_confirmation" type="password" class="form-control form-control-lg"
                           wire:model.defer="password_confirmation" autocomplete="new-password">
                </div>
                <div class="mt-3">
                <button class="btn btn-lg btn-primary" type="submit">{{ __('settings.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</section>
