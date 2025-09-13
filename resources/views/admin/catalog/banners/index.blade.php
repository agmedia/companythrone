@extends('admin.layouts.base-admin')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Companies</h1>
            <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">Add company</a>
        </div>

        <table class="table">
            <thead>
            <tr>
                <th>Title</th>
                <th>Slug</th>
                <th>Group</th>
                <th>Clicks</th>
                <th>Active</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($companies as $c)
                <tr>
                    <td>{{ $c->title }}</td>
                    <td>{{ $c->slug }}</td>
                    <td>{{ $c->group }}</td>
                    <td>{{ $c->clicks }}</td>
                    <td>{{ $c->active ? 'Yes' : 'No' }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.companies.edit', $c) }}">Edit</a>
                        <form action="{{ route('admin.companies.destroy', $c) }}" method="post" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-link text-danger p-0" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">No companies yet.</td></tr>
            @endforelse
            </tbody>
        </table>

        {{ $companies->links() }}
    </div>
@endsection
