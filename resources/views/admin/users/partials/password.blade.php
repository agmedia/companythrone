@extends('back.layouts.base-admin')
@section('title', __('back/users.page_titles.password'))

@section('content')
    @if(session('status') === 'password-updated')
        <div class="alert alert-success mb-3">@lang('back/users.flash.password_updated')</div>
    @endif

    <form method="POST" action="{{ route('settings.password.update') }}" class="card">
        @csrf @method('PUT')
        <div class="card-header"><h5 class="mb-0">@lang('back/users.sections.password')</h5></div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label">@lang('back/users.fields.current_password')</label>
                <input type="password" name="current_password" class="form-control" required>
                @error('current_password') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">@lang('back/users.fields.password')</label>
                <input type="password" name="password" class="form-control" required minlength="8">
                @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">@lang('back/users.fields.password_confirmation')</label>
                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary">@lang('back/common.actions.update')</button>
        </div>
    </form>
@endsection
