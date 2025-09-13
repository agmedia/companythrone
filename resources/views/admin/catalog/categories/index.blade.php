@extends('admin.layouts.base-admin')

@section('title', __('back/categories.title'))

@section('content')
    @php
        // Build a tree from the flat collection returned by the controller
        // (works as long as the query uses defaultOrder()).
        $tree = $categories->toTree();
    @endphp

    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center justify-content-between d-flex">
                    <div>
                        <h5 class="mb-1">{{ __('back/categories.title') }}</h5>
                        @if($group)
                            <div class="small text-muted">
                                {{ __('back/categories.group_label') }}:
                                <span class="text-uppercase">{{ __('back/categories.tabs.'.($group ?? 'companies')) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('catalog.categories.create', ['group'=>$group]) }}" class="btn btn-primary">
                            <i class="ti ti-plus"></i> {{ __('back/common.actions.new') }}
                        </a>
                    </div>
                </div>

                <div class="card-body border-bottom pb-0">
                    @php $groups = ['companies','blog','pages','footer']; @endphp
                    <ul class="nav nav-pills flex-wrap">
                        @foreach($groups as $key)
                            <li class="nav-item me-2 mb-2">
                                <a class="nav-link {{ ($group ?? 'products') === $key ? 'active' : '' }}"
                                   href="{{ route('catalog.categories.index', ['group'=>$key]) }}">
                                    {{ __('back/categories.tabs.'.$key) }}
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
                                <th style="width:56px;">{{ __('back/categories.table.id') }}</th>
                                <th>{{ __('back/categories.table.name') }}</th>
                                <th style="width:140px;">{{ __('back/categories.table.group') }}</th>
                                <th>{{ __('back/categories.table.parent') }}</th>
                                <th style="width:90px;">{{ __('back/categories.table.sort') }}</th>
                                <th style="width:110px;">{{ __('back/categories.table.status') }}</th>
                                <th style="width:150px;">{{ __('back/categories.table.updated') }}</th>
                                <th class="text-end" style="width:120px;">{{ __('back/categories.table.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($tree as $node)
                                @include('admin.catalog.categories.partials.row', ['node' => $node, 'level' => 0, 'group' => $group])
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">{{ __('back/categories.empty') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(method_exists($categories, 'links'))
                    <div class="card-footer">
                        {{ $categories->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
