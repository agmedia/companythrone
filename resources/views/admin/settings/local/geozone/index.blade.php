@extends('admin.layouts.base-admin')

@section('title', __('back/shop/geozone.title'))

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-1">{{ __('back/shop/geozone.title') }}</h5>
                        <div class="small text-muted">{{ __('back/shop/geozone.list') }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('settings.geozones.edit') }}" class="btn btn-primary">
                            <i class="ti ti-plus"></i> {{ __('back/shop/geozone.new') }}
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width:56px;">#</th>
                                <th style="width:40%;">{{ __('back/shop/geozone.input_title') }}</th>
                                <th class="text-center" style="width:120px;">{{ __('back/shop/geozone.countries') }}</th>
                                <th class="text-center" style="width:120px;">{{ __('back/shop/geozone.zones') }}</th>
                                <th class="text-center" style="width:120px;">{{ __('back/shop/geozone.sort_order') }}</th>
                                <th class="text-center" style="width:120px;">{{ __('back/shop/geozone.status_title') }}</th>
                                <th class="text-end" style="width:160px;">{{ __('back/shop/geozone.edit_title') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($items as $it)
                                <tr>
                                    <td>{{ $it->id }}</td>
                                    <td>{{ $it->title->{current_locale()} ?? ($it->title->en ?? '') }}</td>
                                    <td class="text-center">
                                        {{ isset($it->countries_count) ? (int)$it->countries_count : (is_iterable($it->state ?? null) ? count((array)$it->state) : 0) }}
                                    </td>
                                    <td class="text-center">{{ isset($it->zones_count) ? (int)$it->zones_count : 0 }}</td>
                                    <td class="text-center">{{ (int)($it->sort_order ?? 0) }}</td>
                                    <td class="text-center">
                                        @if(!empty($it->status))
                                            <span class="badge bg-success">{{ __('back/common.status.active') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('back/common.status.hidden') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('settings.geozones.edit', ['id' => $it->id]) }}" class="btn btn-sm btn-light" title="{{ __('back/common.actions.edit') }}">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="deleteGeo({{ (int)$it->id }});" title="{{ __('back/common.actions.delete') }}">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">{{ __('back/shop/geozone.empty_list') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Delete modal --}}
    <div class="modal fade" id="delete-geo-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ __('back/shop/geozone.delete_geozone') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h5 class="mb-0">{{ __('back/shop/geozone.delete_shure') }}</h5>
                    <input type="hidden" id="delete-geo-id" value="0">
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('back/shop/geozone.cancel') }}</button>
                    <button class="btn btn-danger" onclick="confirmDeleteGeo();">{{ __('back/shop/geozone.delete') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Axios CSRF
        if (window.axios) {
            window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        }

        window.deleteGeo = function(id) {
            document.getElementById('delete-geo-id').value = id;
            new bootstrap.Modal(document.getElementById('delete-geo-modal')).show();
        };

        window.confirmDeleteGeo = function() {
            const id = document.getElementById('delete-geo-id').value;
            axios.delete('/api/v1/settings/geozones', { data: { id } })
            .then(r => { if (r.data?.success) location.reload(); else (errorToast && errorToast.fire(r.data.message || 'Error')); })
            .catch(() => errorToast && errorToast.fire('Network error'));
        };
    </script>
@endpush
