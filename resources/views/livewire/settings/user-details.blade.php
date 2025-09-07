<div class="w-100">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h2 class="h5 mb-1">{{ __('settings.details_title') }}</h2>
            <p class="text-muted small mb-3">{{ __('settings.details_subtitle') }}</p>

            @if (session('status') === 'user-details-updated')
                <div class="alert alert-success py-2 px-3 mb-3">{{ __('settings.details_updated') }}</div>
            @endif

            <form wire:submit.prevent="save" class="vstack gap-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="fname">{{ __('settings.fname') }}</label>
                        <input id="fname" type="text" class="form-control @error('fname') is-invalid @enderror"
                               wire:model.defer="fname" autocomplete="given-name">
                        @error('fname') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="lname">{{ __('settings.lname') }}</label>
                        <input id="lname" type="text" class="form-control @error('lname') is-invalid @enderror"
                               wire:model.defer="lname" autocomplete="family-name">
                        @error('lname') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label" for="address">{{ __('settings.address') }}</label>
                        <input id="address" type="text" class="form-control @error('address') is-invalid @enderror"
                               wire:model.defer="address" autocomplete="street-address">
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="zip">{{ __('settings.zip') }}</label>
                        <input id="zip" type="text" class="form-control @error('zip') is-invalid @enderror"
                               wire:model.defer="zip" autocomplete="postal-code">
                        @error('zip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="city">{{ __('settings.city') }}</label>
                        <input id="city" type="text" class="form-control @error('city') is-invalid @enderror"
                               wire:model.defer="city" autocomplete="address-level2">
                        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="state">{{ __('settings.state') }}</label>
                        <input id="state" type="text" class="form-control @error('state') is-invalid @enderror"
                               wire:model.defer="state" autocomplete="address-level1">
                        @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="phone">{{ __('settings.phone') }}</label>
                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror"
                               wire:model.defer="phone" autocomplete="tel">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="avatarUpload">{{ __('settings.avatar') }}</label>
                        <input id="avatarUpload" type="file" class="form-control @error('avatarUpload') is-invalid @enderror"
                               wire:model="avatarUpload" accept="image/*">
                        @error('avatarUpload') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        @if($avatar || $avatarUpload)
                            <div class="mt-2">
                                <img
                                        src="{{ $avatarUpload ? $avatarUpload->temporaryUrl() : asset($avatar) }}"
                                        alt="avatar preview" class="rounded" style="max-height:80px">
                            </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="bio">{{ __('settings.bio') }}</label>
                        <textarea id="bio" rows="3" class="form-control @error('bio') is-invalid @enderror"
                                  wire:model.defer="bio"></textarea>
                        @error('bio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label" for="social">{{ __('settings.social') }}</label>
                        <input id="social" type="text" class="form-control @error('social') is-invalid @enderror"
                               wire:model.defer="social" placeholder="https://...">
                        @error('social') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="role">{{ __('settings.role') }}</label>
                        <select id="role" class="form-select @error('role') is-invalid @enderror" wire:model.defer="role">
                            <option value="customer">{{ __('settings.role_customer') }}</option>
                            <option value="editor">{{ __('settings.role_editor') }}</option>
                            <option value="manager">{{ __('settings.role_manager') }}</option>
                            <option value="admin">{{ __('settings.role_admin') }}</option>
                            <option value="master">{{ __('settings.role_master') }}</option>
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label d-block">{{ __('settings.status') }}</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status" wire:model="status">
                            <label class="form-check-label" for="status">
                                {{ $status ? __('settings.active') : __('settings.inactive') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">{{ __('settings.save_details') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
