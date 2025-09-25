{{-- resources/views/back/settings/shop/currency.blade.php --}}
@extends('admin.layouts.base-admin')

@section('title', __('back/shop/currency.title'))

@push('styles')
    {{-- Choices.js is included by your theme; no extra CSS needed here --}}
@endpush

@php
    // Drive locale tabs from config/shop.php → 'locales' => ['hr' => 'Hrvatski', 'en' => 'English', ...]
    $locales = config('shop.locales', ['hr' => 'Hrvatski', 'en' => 'English']);

    // Build a JS-friendly array: [{code:'hr', name:'Hrvatski'}, ...]
    $localesForJs = collect($locales)->map(function($name, $code) {
        return ['code' => $code, 'name' => $name];
    })->values();
@endphp

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header align-items-center justify-content-between d-flex">
                    <div>
                        <h5 class="mb-1">{{ __('back/shop/currency.title') }}</h5>
                        <div class="small text-muted">{{ __('back/shop/currency.list') }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary" onclick="openMainModal();">
                            <i class="ti ti-star"></i> {{ __('back/shop/currency.select_main') }}
                        </button>
                        <button class="btn btn-primary" onclick="openModal();">
                            <i class="ti ti-plus"></i> {{ __('back/shop/currency.new') }}
                        </button>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success m-3 mb-0">{{ session('success') }}</div>
                @endif

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width:56px;">#</th>
                                <th style="width:60%;">{{ __('back/shop/currency.input_title') }}</th>
                                <th class="text-center">{{ __('back/shop/currency.code') }}</th>
                                <th class="text-center">{{ __('back/shop/currency.status_title') }}</th>
                                <th class="text-end" style="width:120px;">{{ __('back/shop/currency.edit_title') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>
                                        @include('admin.settings.partials.lang-list-title', ['item' => $item])
                                        @if (!empty($item->main))
                                            <span class="badge bg-primary">{{ __('back/shop/currency.default_currency') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->code }}</td>
                                    <td class="text-center">
                                        @include('admin.settings.partials.list-status', ['item' => $item])
                                    </td>
                                    <td class="text-end">
                                        @include('admin.settings.partials.list-action-buttons', ['item' => $item, 'editHandler' => 'openModal', 'deleteHandler' => 'deleteCurrency'])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">{{ __('back/shop/currency.empty_list') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- If you paginate later, add a footer with links --}}
                {{-- <div class="card-footer"> {{ $items->links() }} </div> --}}
            </div>
        </div>
    </div>
@endsection

@push('modals')
    {{-- Create/Edit --}}
    <div class="modal fade" id="currency-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-3">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">{{ __('back/shop/currency.edit_title') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-10 mx-auto">
                            <div class="mb-3">
                                <label class="form-label">{{ __('back/shop/currency.input_title') }}</label>

                                {{-- Small tab-like pills driven by config('shop.locales') --}}
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
                                            <input type="text" class="form-control" id="currency-title-{{ $code }}" placeholder="{{ $name }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('back/shop/currency.code') }}</label>
                                <input type="text" class="form-control" id="currency-code">
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('back/shop/currency.symbol_left') }}</label>
                                    <input type="text" class="form-control" id="currency-symbol-left">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('back/shop/currency.symbol_right') }}</label>
                                    <input type="text" class="form-control" id="currency-symbol-right">
                                </div>
                            </div>

                            <div class="row g-3 mt-0">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('back/shop/currency.value') }}</label>
                                    <input type="text" class="form-control" id="currency-value">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('back/shop/currency.decimal') }}</label>
                                    <input type="text" class="form-control" id="currency-decimal-places" value="2">
                                </div>
                            </div>

                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" id="currency-status" checked>
                                <label class="form-check-label" for="currency-status">{{ __('back/shop/currency.status_title') }}</label>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="currency-main">
                                <label class="form-check-label" for="currency-main">{{ __('back/shop/currency.default_currency') }}</label>
                            </div>

                            <input type="hidden" id="currency-id" value="0">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('back/shop/currency.cancel') }}</button>
                    <button class="btn btn-primary" onclick="createCurrency();">{{ __('back/shop/currency.save') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Set main --}}
    <div class="modal fade" id="main-currency-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">{{ __('back/shop/currency.select_main') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <select class="form-control" id="currency-main-select" data-placeholder="{{ __('back/shop/currency.select_main') }}">
                            <option value="">{{ __('back/shop/currency.select_main') }}</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" {{ ($main && $main->id == $item->id) ? 'selected' : '' }}>
                                    {{ isset($item->title->{current_locale()}) ? $item->title->{current_locale()} : $item->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('back/shop/currency.cancel') }}</button>
                    <button class="btn btn-primary" onclick="storeMainCurrency();">{{ __('back/shop/currency.save') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete --}}
    <div class="modal fade" id="delete-currency-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ __('back/shop/currency.delete_tax') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h5 class="mb-0">{{ __('back/shop/currency.delete_shure') }}</h5>
                    <input type="hidden" id="delete-currency-id" value="0">
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('back/shop/currency.cancel') }}</button>
                    <button class="btn btn-danger" onclick="confirmDelete();">{{ __('back/shop/currency.delete') }}</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    {{-- Choices.js is included by your theme; we just initialize it --}}
    <script>
        // Axios CSRF
        if (window.axios) {
            window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        }

        // Build LANGS array like: [{code:'hr', name:'Hrvatski'}, ...] from PHP
        const LANGS = @json($localesForJs);

        let currencyMainChoices = null;

        document.addEventListener('DOMContentLoaded', () => {
            if (window.Choices) {
                currencyMainChoices = new Choices('#currency-main-select', {
                    searchEnabled: false,
                    shouldSort: false,
                    allowHTML: false,
                    placeholder: true,
                    placeholderValue: document.getElementById('currency-main-select')?.dataset?.placeholder || '',
                    itemSelectText: '',
                });
            } else {
                console.warn('Choices.js not found. Ensure the theme includes it before stack(scripts).');
            }

            // Optional UX: disable "main" if disabled
            const statusEl = document.getElementById('currency-status');
            if (statusEl) {
                statusEl.addEventListener('change', function() {
                    const on = this.checked;
                    document.getElementById('currency-main').disabled = !on;
                    if (!on) document.getElementById('currency-main').checked = false;
                });
            }
        });

        // ---- Expose functions globally for inline onclick ----
        window.openModal = function(item = null) {
            resetCurrencyForm();
            if (item) fillCurrencyForm(item);
            new bootstrap.Modal(document.getElementById('currency-modal')).show();
        };

        window.openMainModal = function() {
            new bootstrap.Modal(document.getElementById('main-currency-modal')).show();
        };

        window.resetCurrencyForm = function() {
            document.getElementById('currency-id').value = 0;
            document.getElementById('currency-code').value = '';
            document.getElementById('currency-symbol-left').value = '';
            document.getElementById('currency-symbol-right').value = '';
            document.getElementById('currency-value').value = '';
            document.getElementById('currency-decimal-places').value = '2';
            document.getElementById('currency-status').checked = true;
            document.getElementById('currency-status').dispatchEvent(new Event('change'));
            document.getElementById('currency-main').checked = false;

            LANGS.forEach(l => {
                const el = document.getElementById('currency-title-' + l.code);
                if (el) el.value = '';
            });
        };

        window.fillCurrencyForm = function(item) {
            document.getElementById('currency-id').value = item.id || 0;
            document.getElementById('currency-code').value = item.code || '';
            document.getElementById('currency-symbol-left').value = item.symbol_left || '';
            document.getElementById('currency-symbol-right').value = item.symbol_right || '';
            document.getElementById('currency-value').value = item.value || '';
            document.getElementById('currency-decimal-places').value = item.decimal_places || '2';

            LANGS.forEach(l => {
                const el = document.getElementById('currency-title-' + l.code);
                if (el && item.title && typeof item.title[l.code] !== 'undefined') {
                    el.value = item.title[l.code];
                }
            });

            document.getElementById('currency-status').checked = !!item.status;
            document.getElementById('currency-status').dispatchEvent(new Event('change'));
            document.getElementById('currency-main').checked = !!item.main;
        };

        window.createCurrency = function() {
            const titles = {};
            LANGS.forEach(l => {
                const el = document.getElementById('currency-title-' + l.code);
                titles[l.code] = (el?.value || '').trim();
            });

            const rawCode  = (document.getElementById('currency-code').value || '').trim().toUpperCase();
            const valueNum = Number((document.getElementById('currency-value').value || '').toString().replace(',', '.'));
            const decimals = parseInt(document.getElementById('currency-decimal-places').value || '2', 10);

            if (!rawCode) return errorToast.fire("{{ __('back/shop/currency.code') }} {{ __('back/shop/currency.required') }}");
            if (isNaN(valueNum) || valueNum <= 0) return errorToast.fire("{{ __('back/shop/currency.value') }} {{ __('back/shop/currency.invalid') }}");
            if (isNaN(decimals) || decimals < 0 || decimals > 6) return errorToast.fire("{{ __('back/shop/currency.decimal') }} {{ __('back/shop/currency.invalid') }}");

            const item = {
                id: parseInt(document.getElementById('currency-id').value || '0', 10),
                title: titles,
                code: rawCode,
                symbol_left: (document.getElementById('currency-symbol-left').value || '').trim(),
                symbol_right: (document.getElementById('currency-symbol-right').value || '').trim(),
                value: valueNum,
                decimal_places: decimals,
                status: !!document.getElementById('currency-status').checked,
                main: !!document.getElementById('currency-main').checked
            };

            axios.post('/api/v1/settings/currencies', { data: item })
            .then(r => {
                if (r.data && r.data.success) location.reload();
                else errorToast.fire(r.data.message || 'Error');
            })
            .catch(() => errorToast.fire('Network error'));
        };

        window.storeMainCurrency = function() {
            let selected = document.getElementById('currency-main-select').value;
            if (window.currencyMainChoices && typeof currencyMainChoices.getValue === 'function') {
                const choice = currencyMainChoices.getValue(true);
                if (choice !== undefined && choice !== null) selected = choice;
            }
            axios.post('/api/v1/settings/currencies/main', { data: { main: selected } })
            .then(r => { if (r.data?.success) location.reload(); else errorToast.fire(r.data.message || 'Error'); })
            .catch(() => errorToast.fire('Network error'));
        };

        window.deleteCurrency = function(id) {
            const mainId = {{ $main ? (int)$main->id : 0 }};
            if (parseInt(id, 10) === mainId) return errorToast.fire("{{ __('back/shop/currency.cannot_delete_main') }}");
            document.getElementById('delete-currency-id').value = id;
            new bootstrap.Modal(document.getElementById('delete-currency-modal')).show();
        };

        window.confirmDelete = function() {
            axios.delete('/api/v1/settings/currencies', { data: { id: document.getElementById('delete-currency-id').value } })
            .then(r => { if (r.data?.success) location.reload(); else errorToast.fire(r.data.message || 'Error'); })
            .catch(() => errorToast.fire('Network error'));
        };
    </script>
@endpush
