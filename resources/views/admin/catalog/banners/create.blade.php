@extends('admin.layouts.base-admin')

@section('content')
    <div class="container">
        <h1>Create company</h1>

        <form action="{{ route('admin.companies.store') }}" method="post">
            @csrf
            <div class="mb-3">
                <label>Title</label>
                <input name="title" class="form-control" value="{{ old('title') }}">
            </div>

            <div class="mb-3">
                <label>Slug</label>
                <input name="slug" class="form-control" value="{{ old('slug') }}">
            </div>

            <div class="mb-3">
                <label>Group</label>
                <input name="group" class="form-control" value="{{ old('group') }}">
            </div>

            <div class="mb-3">
                <label>Clicks</label>
                <input type="number" name="clicks" class="form-control" value="{{ old('clicks', 0) }}">
            </div>

            <div class="form-check mb-3">
                <input type="hidden" name="active" value="0">
                <input class="form-check-input" type="checkbox" name="active" value="1" id="active">
                <label class="form-check-label" for="active">Active</label>
            </div>

            <button class="btn btn-primary">Save</button>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
