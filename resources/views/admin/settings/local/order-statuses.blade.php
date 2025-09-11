{{-- resources/views/back/settings/shop/order-statuses.blade.php --}}
@extends('back.layouts.base-admin')

@section('title', __('back/shop/statuses.title'))

@push('styles')
    {{-- Theme already includes needed assets --}}
@endpush

@php
    // Same locales driver as currencies
    $locales = config('shop.locales', ['hr' => 'Hrvatski', 'en' => 'English']);
    $localesForJs = collect($locales)->map(fn($name,$code) => ['code'=>$code,'name'=>$name])->values();
@endphp

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header align-items-center justify-content-between d-flex">
                    <div>
                        <h5 class="mb-1">{{ __('back/shop/statuses.title') }}</h5>
                        <div class="small text-muted">{{ __('back/shop/statuses.list') }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" onclick="openStatusModal();">
                            <i class="ti ti-plus"></i> {{ __('back/shop/statuses.new') }}
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width:56px;">#</th>
                                <th style="width:45%;">{{ __('back/shop/statuses.input_title') }}</th>
                                <th class="text-center" style="width:160px;">{{ __('back/shop/statuses.color') }}</th>
                                <th class="text-center" style="width:120px;">{{ __('back/shop/statuses.sort_order') }}</th>
                                <th class="text-end" style="width:120px;">{{ __('back/shop/statuses.edit_title') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>
                                        @include('back.settings.partials.lang-list-title', ['item' => $item])
                                    </td>
                                    <td class="text-center">
                                        @php $c = $item->color ?: 'light'; @endphp
                                        <span class="badge bg-{{ $c }}">
                                            {{ $item->title->{current_locale()} ?? ($item->title->en ?? $item->title) }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $item->sort_order }}</td>
                                    <td class="text-end">
                                        @include('back.settings.partials.list-action-buttons', ['item' => $item, 'editHandler' => 'openStatusModal', 'deleteHandler' => 'deleteStatus'])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">{{ __('back/shop/statuses.empty_list') }}</td>
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

@push('modals')
    {{-- Create/Edit Order statuses (now same HTML skeleton as currencies) --}}
    <div class="modal fade" id="status-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-3">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">{{ __('back/shop/statuses.main_title') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-10 mx-auto">

                            {{-- Title with language pills (exactly like currencies) --}}
                            <div class="mb-3">
                                <label class="form-label">{{ __('back/shop/statuses.input_title') }}</label>

                                <ul class="nav nav-pills flex-wrap justify-content-end mb-2">
                                    @foreach($locales as $code => $name)
                                        <li class="nav-item me-2 mb-2">
                                            <a class="nav-link @if ($code == current_locale()) active @endif"
                                               data-bs-toggle="pill" href="#title-{{ $code }}">
                                                <img class="me-1" width="18" src="{{ asset('media/flags/' . $code . '.png') }}" />
                                                {{ strtoupper($code) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content">
                                    @foreach($locales as $code => $name)
                                        <div id="title-{{ $code }}" class="tab-pane fade @if ($code == current_locale()) show active @endif">
                                            <input type="text" class="form-control" id="status-title-input-{{ $code }}"
                                                   name="title[{{ $code }}]" placeholder="{{ $name }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="status-sort-order" class="form-label">{{ __('back/shop/statuses.sort_order') }}</label>
                                <input type="text" class="form-control" id="status-sort-order" name="sort_order" value="0">
                            </div>

                            <div class="mb-1">
                                <label for="status-color-select" class="form-label d-flex align-items-center justify-content-between">
                                    <span>{{ __('back/shop/statuses.color') }}</span>
                                    <span id="status-color-preview" class="badge bg-light text-dark">—</span>
                                </label>
                                <select class="form-control" id="status-color-select" data-placeholder="{{ __('back/shop/statuses.select_status') }}">
                                    <option value="primary">Primary</option>
                                    <option value="secondary">Secondary</option>
                                    <option value="success">Success</option>
                                    <option value="info">Info</option>
                                    <option value="light">Light</option>
                                    <option value="danger">Danger</option>
                                    <option value="warning">Warning</option>
                                    <option value="dark">Dark</option>
                                </select>
                            </div>

                            <input type="hidden" id="status-id" value="0">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('back/shop/statuses.cancel') }}</button>
                    <button class="btn btn-primary" onclick="createStatus();">{{ __('back/shop/statuses.save') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete (also matching currencies markup) --}}
    <div class="modal fade" id="delete-status-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ __('back/shop/statuses.delete_status') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h5 class="mb-0">{{ __('back/shop/statuses.delete_shure') }}</h5>
                    <input type="hidden" id="delete-status-id" value="0">
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('back/shop/statuses.cancel') }}</button>
                    <button class="btn btn-danger" onclick="confirmDeleteStatus();">{{ __('back/shop/statuses.delete') }}</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        // Axios CSRF (same as currencies)
        if (window.axios) {
            window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        }

        const LANGS = @json($localesForJs);
        let statusColorChoices = null;

        document.addEventListener('DOMContentLoaded', () => {
            if (window.Choices) {
                statusColorChoices = new Choices('#status-color-select', {
                    searchEnabled: false,
                    shouldSort: false,
                    allowHTML: false,
                    itemSelectText: '',
                    placeholder: true
                });
            }

            const colorSelect = document.getElementById('status-color-select');
            const preview = document.getElementById('status-color-preview');
            const updatePreview = () => {
                const val = colorSelect.value || 'light';
                preview.className = 'badge bg-' + val;
                preview.textContent = val.charAt(0).toUpperCase() + val.slice(1);
            };
            colorSelect.addEventListener('change', updatePreview);
            updatePreview();
        });

        // ---- CRUD (unchanged) ----
        window.openStatusModal = function(item = null) {
            resetStatusForm();
            if (item) fillStatusForm(item);
            new bootstrap.Modal(document.getElementById('status-modal')).show();
        };

        window.resetStatusForm = function() {
            document.getElementById('status-id').value = 0;
            document.getElementById('status-sort-order').value = '0';
            const colorSelect = document.getElementById('status-color-select');
            if (statusColorChoices) statusColorChoices.setChoiceByValue('light'); else colorSelect.value = 'light';
            colorSelect.dispatchEvent(new Event('change'));
            LANGS.forEach(l => {
                const el = document.getElementById('status-title-input-' + l.code);
                if (el) el.value = '';
            });
        };

        window.fillStatusForm = function(item) {
            document.getElementById('status-id').value = item.id || 0;
            document.getElementById('status-sort-order').value = (item.sort_order ?? '0');
            const color = item.color || 'light';
            if (statusColorChoices) statusColorChoices.setChoiceByValue(color);
            else document.getElementById('status-color-select').value = color;
            document.getElementById('status-color-select').dispatchEvent(new Event('change'));

            LANGS.forEach(l => {
                const el = document.getElementById('status-title-input-' + l.code);
                if (el && item.title && typeof item.title[l.code] !== 'undefined') el.value = item.title[l.code];
            });
        };

        window.createStatus = function() {
            const titles = {};
            LANGS.forEach(l => {
                const el = document.getElementById('status-title-input-' + l.code);
                titles[l.code] = (el?.value || '').trim();
            });

            const item = {
                id: parseInt(document.getElementById('status-id').value || '0', 10),
                title: titles,
                sort_order: (document.getElementById('status-sort-order').value || '0'),
                color: document.getElementById('status-color-select').value || 'light'
            };

            axios.post('/api/v1/settings/statuses', { data: item })
            .then(r => { if (r.data && r.data.success) location.reload(); else (errorToast && errorToast.fire(r.data.message || 'Error')); })
            .catch(() => errorToast && errorToast.fire('Network error'));
        };

        window.deleteStatus = function(id) {
            document.getElementById('delete-status-id').value = id;
            new bootstrap.Modal(document.getElementById('delete-status-modal')).show();
        };

        window.confirmDeleteStatus = function() {
            const id = document.getElementById('delete-status-id').value;
            axios.delete('/api/v1/settings/statuses', { data: { id } })
            .then(r => { if (r.data?.success) location.reload(); else (errorToast && errorToast.fire(r.data.message || 'Error')); })
            .catch(() => errorToast && errorToast.fire('Network error'));
        };
    </script>
@endpush
