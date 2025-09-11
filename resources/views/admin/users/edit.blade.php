@extends('back.layouts.base-admin')
@section('title', __('back/users.page_titles.edit'))

@section('content')
    <form method="POST" action="{{ route('users.update', $detail) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('back.users.partials.form')
        <div class="d-flex gap-2">
            <button class="btn btn-primary">@lang('back/common.actions.update')</button>
            <a href="{{ route('users.index', ['role' => $detail->role]) }}" class="btn btn-light">
                @lang('back/common.actions.cancel')
            </a>
        </div>
    </form>
@endsection
