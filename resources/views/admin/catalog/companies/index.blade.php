@extends('admin.layouts.base-admin')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center justify-content-between d-flex">
                    <h5 class="mb-1">{{ __('back/companies.title') }}</h5>
                    <a href="{{ route('catalog.companies.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus"></i> {{ __('back/common.actions.new') }}
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success m-3 mb-0">{{ session('success') }}</div>
                @endif

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width:56px;">{{ __('back/companies.table.id') }}</th>
                                <th>Naziv</th>
                                <th>Level</th>
                                <th>Grad</th>
                                <th class="text-center">Klikova</th>
                                <th class="text-center">Referrals</th>
                                <th class="text-center" style="width: 25%;">Tvrtka Aktivirana / Dnevni zadaci</th>
                                <th class="text-end" style="width:120px;">{{ __('back/companies.table.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($companies as $com)
                                <tr>
                                    <td>{{ $com->id }}</td>
                                    <td>{{ $com?->t_name ?? '' }}</td> {{-- blank if no parent --}}
                                    <td>{{ $com->level ? $com->level->number : '-' }}</td>
                                    <td>{{ $com->city }}</td>
                                    <td class="text-center">{{ $com->clicks }}</td>
                                    <td class="text-center">{{ $com->referrals_count }}</td>
                                    <td class="text-center">
                                        @if($com->is_published)
                                            <span class="badge text-bg-success">{{ __('back/common.status.active') }}</span>
                                        @else
                                            <span class="badge text-bg-danger">{{ __('back/common.status.hidden') }}</span>
                                        @endif
                                        @if($com->is_link_active)
                                            <span class="badge text-bg-success">{{ __('Da') }}</span>
                                        @else
                                            <span class="badge text-bg-danger">{{ __('Ne') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2">
                                            <a href="{{ route('catalog.companies.edit', $com) }}"
                                               class="btn btn-sm btn-outline-primary rounded-circle" title="{{ __('back/common.actions.edit') }}">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <form action="{{ route('catalog.companies.destroy', $com) }}" method="POST"
                                                  onsubmit="return confirm('{{ __('back/companies.confirm_delete') }}')" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger rounded-circle" title="{{ __('back/common.actions.delete') }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7">No companies yet.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(method_exists($companies, 'links'))
                    <div class="card-footer">
                        {{ $companies->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
