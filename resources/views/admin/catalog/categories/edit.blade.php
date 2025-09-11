@extends('back.layouts.base-admin')

@section('title', __('back/categories.title_edit'))

@section('content')
    @php $t = $category->translation(); @endphp
    <div class="row g-3">
        <div class="col-12 col-lg-10">
            <form action="{{ route('catalog.categories.update', $category) }}" method="POST" class="card" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        {{ __('back/categories.title_edit') }}: {{ $t?->title ?? '—' }}
                    </h5>
                    <a href="{{ route('catalog.categories.index', ['group'=>request('group','products')]) }}"
                       class="btn btn-light">{{ __('back/common.actions.back') }}</a>
                </div>
                <div class="card-body">
                    @include('back.catalog.categories.partials.form', ['mode' => 'edit'])
                </div>
                <div class="card-footer d-flex gap-2">
                    <button class="btn btn-primary">{{ __('back/common.actions.update') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
