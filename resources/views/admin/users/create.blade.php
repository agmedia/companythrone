@extends('back.layouts.base-admin')
@section('title', __('back/users.page_titles.create'))

@section('content')
    <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
        @csrf
        @include('back.users.partials.form')
        <div class="d-flex gap-2">
            <button class="btn btn-primary">@lang('back/common.actions.create')</button>
            <a href="{{ route('users.index', ['role' => request('role','customer')]) }}" class="btn btn-light">
                @lang('back/common.actions.cancel')
            </a>
        </div>
    </form>
@endsection
