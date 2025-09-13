@extends('admin.layouts.base-admin')

@section('content')
    <div class="container">
        <h1>Edit company</h1>

        <form action="{{ route('admin.companies.update', $company) }}" method="post">
            @csrf @method('PUT')

            <div class="mb-3">
                <label>Title</label>
                <input name="title" class="form-control" value="{{ old('title', $company->title) }}">
            </div>

            <div class="mb-3">
                <label>Slug</label>
                <input name="slug" class="form-control" value="{{ old('slug', $company->slug) }}">
            </div>

            <div class="mb-3">
                <label>Group</label>
                <input name="group" class="form-control" value="{{ old('group', $company->group) }}">
            </div>

            <div class="mb-3">
                <label>Clicks</label>
                <input type="number" name="clicks" class="form-control" value="{{ old('clicks', $company->clicks) }}">
            </div>

            <div class="form-check mb-3">
                <input type="hidden" name="active" value="0">
                <input class="form-check-input" type="checkbox" name="active" value="1" id="active" {{ old('active', $company->active) ? 'checked' : '' }}>
                <label class="form-check-label" for="active">Active</label>
            </div>

            <button class="btn btn-primary">Update</button>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
