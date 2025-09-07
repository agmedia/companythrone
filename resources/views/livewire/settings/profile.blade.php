<section class="w-100">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5 mb-1">{{ __('settings.profile_title') }}</h2>
            <p class="text-muted small mb-3">{{ __('settings.profile_subtitle') }}</p>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success py-2 px-3">{{ __('settings.profile_updated') }}</div>
            @endif

            <form wire:submit.prevent="updateProfileInformation" class="vstack gap-3">
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

                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">{{ __('settings.save') }}</button>

                    @if (auth()->user()?->hasVerifiedEmail() === false)
                        <button class="btn btn-outline-secondary" type="button" wire:click="resendVerificationNotification">
                            {{ __('settings.resend_verification') }}
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
    @livewire('settings.user-details')
</section>
