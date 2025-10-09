@extends('admin.layouts.base-admin')

@section('title', __('back/users.title'))

@section('content')
    @php
        $roles = \App\Models\UserDetail::ROLES;
    @endphp

    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ __('back/users.title') }}</h5>
                        <div class="small text-muted">
                            {{ __('back/users.role_label') }}:
                            <span class="text-uppercase">{{ __('back/users.tabs.'.$role) }}</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                       {{--  <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus"></i> {{ __('back/common.actions.new') }}
                        </a>--}}
                    </div>
                </div>

                <div class="card-body border-bottom pb-0">
                    <ul class="nav nav-pills flex-wrap">
                        @foreach($roles as $r)
                            <li class="nav-item me-2 mb-2">
                                <a class="nav-link {{ $role === $r ? 'active' : '' }}"
                                   href="{{ route('users.index', ['role'=>$r]) }}">
                                    {{ __('back/users.tabs.'.$r) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if(session('success'))
                    <div class="alert alert-success m-3 mb-0">{{ session('success') }}</div>
                @endif

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width:56px;">ID</th>
                                <th>{{ __('back/users.table.user') }}</th>
                                <th>{{ __('back/users.table.email') }}</th>
                                <th>{{ __('back/users.table.phone') }}</th>
                                <th>{{ __('back/users.table.city') }}</th>
                                <th style="width:110px;">{{ __('back/users.table.status') }}</th>
                                <th style="width:160px;">{{ __('back/users.table.updated') }}</th>
                                <th class="text-end" style="width:120px;">{{ __('back/users.table.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $detail)
                                @include('admin.users.partials.row', ['detail' => $detail])
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">{{ __('back/common.no_results') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
