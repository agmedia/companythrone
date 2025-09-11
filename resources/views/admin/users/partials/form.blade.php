@php
    $isEdit = isset($detail) && $detail->exists;
@endphp

@include('back.users.partials.filepond')

<div class="card mb-3">
    <div class="card-header">
        <h6 class="mb-0">@lang('back/users.sections.basic_info')</h6>
    </div>
    <div class="card-body row g-3">
        <div class="col-md-4">
            <label class="form-label">@lang('back/users.fields.fname')</label>
            <input name="fname" class="form-control" value="{{ old('fname', $detail->fname ?? '') }}" required>
            @error('fname') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">@lang('back/users.fields.lname')</label>
            <input name="lname" class="form-control" value="{{ old('lname', $detail->lname ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">@lang('back/users.fields.email')</label>
            <input name="email" type="email" class="form-control" value="{{ old('email', $detail->user->email ?? '') }}" required>
            @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">@lang('back/users.fields.address')</label>
            <input name="address" class="form-control" value="{{ old('address', $detail->address ?? '') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">@lang('back/users.fields.zip')</label>
            <input name="zip" class="form-control" value="{{ old('zip', $detail->zip ?? '') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">@lang('back/users.fields.city')</label>
            <input name="city" class="form-control" value="{{ old('city', $detail->city ?? '') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label">@lang('back/users.fields.state')</label>
            <input name="state" class="form-control" value="{{ old('state', $detail->state ?? '') }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">@lang('back/users.fields.phone')</label>
            <input name="phone" class="form-control" value="{{ old('phone', $detail->phone ?? '') }}">
        </div>
        <div class="col-md-8">
            <label class="form-label">@lang('back/users.fields.social')</label>
            <input name="social" class="form-control" value="{{ old('social', $detail->social ?? '') }}">
        </div>

        <div class="col-12">
            <label class="form-label">@lang('back/users.fields.bio')</label>
            <textarea name="bio" class="form-control" rows="3">{{ old('bio', $detail->bio ?? '') }}</textarea>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><h6 class="mb-0">@lang('back/users.sections.password')</h6></div>
    <div class="card-body row g-3">
        <div class="col-md-4">
            <label class="form-label">
                @lang('back/users.fields.password')
                @if($isEdit)<small class="text-muted">(@lang('back/users.hints.leave_blank_keep'))</small>@endif
            </label>
            <input type="password" name="password" class="form-control" {{ $isEdit ? '' : 'required' }}>
            @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">@lang('back/users.fields.password_confirmation')</label>
            <input type="password" name="password_confirmation" class="form-control" {{ $isEdit ? '' : 'required' }}>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><h6 class="mb-0">@lang('back/users.sections.role_status')</h6></div>
    <div class="card-body row g-3 align-items-center">
        <div class="col-md-4">
            <label class="form-label">@lang('back/users.fields.role')</label>
            <select name="role" class="form-select">
                @foreach($roles as $r)
                    <option value="{{ $r }}" @selected(old('role', $detail->role ?? request('role','customer')) === $r)>
                        {{ __('back/users.tabs.'.$r) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label d-block">@lang('back/users.fields.status')</label>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="status" value="1"
                        {{ old('status', $detail->status ?? true) ? 'checked' : '' }}>
                <label class="form-check-label">@lang('back/common.status.active')</label>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><h6 class="mb-0">@lang('back/users.sections.avatar')</h6></div>
    <div class="card-body">
        @if(!empty($detail->avatar))
            <div class="d-flex align-items-center gap-3 mb-3">
                <img src="{{ asset($detail->avatar) }}" alt="avatar" class="rounded"
                     style="width:64px;height:64px;object-fit:cover;">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remove_avatar" value="1" id="removeAvatar">
                    <label class="form-check-label" for="removeAvatar">@lang('back/common.actions.remove_image')</label>
                </div>
            </div>
        @endif
        <input type="file" name="avatar_file" accept="image/*">
        <small class="text-muted d-block mt-2">@lang('back/users.hints.avatar_limit')</small>
    </div>
</div>
