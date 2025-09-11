{{-- resources/views/back/settings/shop/taxes.blade.php --}}
@extends('back.layouts.base-admin')

@section('title', __('back/shop/tax.title'))

@push('styles')
    {{-- Theme already includes needed assets --}}
@endpush

@php
    // Match currencies.blade: drive tabs from config('shop.locales')
    $locales = config('shop.locales', ['hr' => 'Hrvatski', 'en' => 'English']);
    $localesForJs = collect($locales)->map(fn($name,$code) => ['code'=>$code,'name'=>$name])->values();
@endphp

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header align-items-center justify-content-between d-flex">
                    <div>
                        <h5 class="mb-1">{{ __('back/shop/tax.title') }}</h5>
                        <div class="small text-muted">{{ __('back/shop/tax.list') }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" onclick="openTaxModal();">
                            <i class="ti ti-plus"></i> {{ __('back/shop/tax.new') }}
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width:56px;">#</th>
                                <th style="width:50%;">{{ __('back/shop/tax.input_title') }}</th>
                                <th class="text-center" style="width:120px;">{{ __('back/shop/tax.stopa') }}</th>
                                <th class="text-center" style="width:120px;">{{ __('back/shop/tax.sort_order') }}</th>
                                <th class="text-center" style="width:120px;">{{ __('back/shop/tax.status_title') }}</th>
                                <th class="text-end" style="width:120px;">{{ __('back/shop/tax.edit_title') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>
                                        @include('back.settings.partials.lang-list-title', ['item' => $item])
                                    </td>
                                    <td class="text-center">{{ $item->rate }}</td>
                                    <td class="text-center">{{ $item->sort_order }}</td>
                                    <td class="text-center">
                                        @include('back.settings.partials.list-status', ['item' => $item])
                                    </td>
                                    <td class="text-end">
                                        @include('back.settings.partials.list-action-buttons', ['item' => $item, 'editHandler' => 'openTaxModal', 'deleteHandler' => 'deleteTax'])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">{{ __('back/shop/tax.empty_list') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- footer for pagination if needed --}}
            </div>
        </div>
    </div>
@endsection

@push('modals')
    {{-- Create/Edit (mirrors currencies modal structure/classes) --}}
    <div class="modal fade" id="tax-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-3">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">{{ __('back/shop/tax.main_title') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-10 mx-auto">
                            <div class="mb-3">
                                <label class="form-label">{{ __('back/shop/tax.input_title') }}</label>

                                {{-- Small tab-like pills (exactly like currencies) --}}
                                <ul class="nav nav-pills flex-wrap justify-content-end mb-2">
                                    @foreach($locales as $code => $name)
                                        <li class="nav-item me-2 mb-2">
                                            <a class="nav-link @if ($code == current_locale()) active @endif"
                                               data-bs-toggle="pill" href="#tax-title-{{ $code }}">
                                                <img class="me-1" width="18" src="{{ asset('media/flags/' . $code . '.png') }}" />
                                                {{ strtoupper($code) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content">
                                    @foreach($locales as $code => $name)
                                        <div id="tax-title-{{ $code }}" class="tab-pane fade @if ($code == current_locale()) show active @endif">
                                            <input type="text" class="form-control" id="tax-title-input-{{ $code }}" placeholder="{{ $name }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('back/shop/tax.stopa') }}</label>
                                    <input type="text" class="form-control" id="tax-rate" placeholder="e.g. 25">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('back/shop/tax.sort_order') }}</label>
                                    <input type="text" class="form-control" id="tax-sort-order" value="0">
                                </div>
                            </div>

                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" id="tax-status" checked>
                                <label class="form-check-label" for="tax-status">{{ __('back/shop/tax.status_title') }}</label>
                            </div>

                            <input type="hidden" id="tax-id" value="0">
                            <input type="hidden" id="tax-geo-zone" value="1">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('back/shop/tax.cancel') }}</button>
                    <button class="btn btn-primary" onclick="createTax();">{{ __('back/shop/tax.save') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete --}}
    <div class="modal fade" id="delete-tax-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ __('back/shop/tax.delete_tax') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h5 class="mb-0">{{ __('back/shop/tax.delete_shure') }}</h5>
                    <input type="hidden" id="delete-tax-id" value="0">
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('back/shop/tax.cancel') }}</button>
                    <button class="btn btn-danger" onclick="confirmDeleteTax();">{{ __('back/shop/tax.delete') }}</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        // Axios CSRF
        if (window.axios) {
            window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        }

        // Same pattern as currencies
        const LANGS = @json($localesForJs);

        // Open / Reset / Fill
        window.openTaxModal = function(item = null) {
            resetTaxForm();
            if (item) fillTaxForm(item);
            new bootstrap.Modal(document.getElementById('tax-modal')).show();
        };

        window.resetTaxForm = function() {
            document.getElementById('tax-id').value = 0;
            document.getElementById('tax-geo-zone').value = '1';
            document.getElementById('tax-rate').value = '';
            document.getElementById('tax-sort-order').value = '0';
            document.getElementById('tax-status').checked = true;

            LANGS.forEach(l => {
                const el = document.getElementById('tax-title-input-' + l.code);
                if (el) el.value = '';
            });
        };

        window.fillTaxForm = function(item) {
            document.getElementById('tax-id').value = item.id || 0;
            document.getElementById('tax-geo-zone').value = item.geo_zone || '1';
            document.getElementById('tax-rate').value = item.rate || '';
            document.getElementById('tax-sort-order').value = item.sort_order || '0';
            document.getElementById('tax-status').checked = !!item.status;

            LANGS.forEach(l => {
                const el = document.getElementById('tax-title-input-' + l.code);
                if (el && item.title && typeof item.title[l.code] !== 'undefined') {
                    el.value = item.title[l.code];
                }
            });
        };

        window.createTax = function() {
            const titles = {};
            LANGS.forEach(l => {
                const el = document.getElementById('tax-title-input-' + l.code);
                titles[l.code] = (el?.value || '').trim();
            });

            const rateStr = (document.getElementById('tax-rate').value || '').toString().trim().replace(',', '.');
            if (!rateStr) return errorToast && errorToast.fire("{{ __('back/shop/tax.stopa') }} {{ __('back/shop/tax.required') }}");

            const item = {
                id: parseInt(document.getElementById('tax-id').value || '0', 10),
                geo_zone: (document.getElementById('tax-geo-zone').value || '1'),
                title: titles,
                rate: rateStr,
                sort_order: (document.getElementById('tax-sort-order').value || '0'),
                status: !!document.getElementById('tax-status').checked
            };

            axios.post('/api/v1/settings/taxes', { data: item })
            .then(r => { if (r.data && r.data.success) location.reload(); else (errorToast && errorToast.fire(r.data.message || 'Error')); })
            .catch(() => errorToast && errorToast.fire('Network error'));
        };

        // Delete
        window.deleteTax = function(id) {
            document.getElementById('delete-tax-id').value = id;
            new bootstrap.Modal(document.getElementById('delete-tax-modal')).show();
        };
        window.confirmDeleteTax = function() {
            const id = document.getElementById('delete-tax-id').value;
            axios.delete('/api/v1/settings/taxes', { data: { id } })
            .then(r => { if (r.data?.success) location.reload(); else (errorToast && errorToast.fire(r.data.message || 'Error')); })
            .catch(() => errorToast && errorToast.fire('Network error'));
        };
    </script>
@endpush
