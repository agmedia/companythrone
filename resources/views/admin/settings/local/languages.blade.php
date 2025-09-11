{{-- resources/views/back/settings/shop/languages.blade.php --}}
@extends('back.layouts.base-admin')

@section('title', __('back/shop/languages.title'))

@push('styles')
    {{-- Theme already includes required CSS (Choices etc.) --}}
@endpush

@php
    // Drive locale tabs from config/shop.php → 'locales' => ['hr' => 'Hrvatski', 'en' => 'English', ...]
    $locales = config('shop.locales', ['hr' => 'Hrvatski', 'en' => 'English']);

    // Build a JS-friendly array: [{code:'hr', name:'Hrvatski'}, ...]
    $localesForJs = collect($locales)->map(fn($name, $code) => ['code' => $code, 'name' => $name])->values();
@endphp

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header align-items-center justify-content-between d-flex">
                    <div>
                        <h5 class="mb-1">{{ __('back/shop/languages.title') }}</h5>
                        <div class="small text-muted">{{ __('back/shop/languages.list') }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" onclick="openLangModal();">
                            <i class="ti ti-plus"></i> {{ __('back/shop/languages.new') }}
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
                                <th style="width:60%;">{{ __('back/shop/languages.table_title') }}</th>
                                <th class="text-center">{{ __('back/shop/languages.code_title') }}</th>
                                <th class="text-center">{{ __('back/shop/languages.status_title') }}</th>
                                <th class="text-end" style="width:120px;">{{ __('back/shop/languages.edit_title') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td class="text-primary">
                                        @include('back.settings.partials.lang-list-title', ['item' => $item])
                                        @if (!empty($item->main))
                                            <span class="badge bg-primary ms-2">{{ __('back/shop/languages.default_lang') }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->code }}</td>
                                    <td class="text-center">
                                        @include('back.settings.partials.list-status', ['item' => $item])
                                    </td>
                                    <td class="text-end">
                                        @include('back.settings.partials.list-action-buttons', ['item' => $item, 'editHandler' => 'openLangModal', 'deleteHandler' => 'deleteLanguage'])
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">{{ __('back/shop/languages.empty_list') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- footer for pagination (if needed later) --}}
            </div>
        </div>
    </div>
@endsection

@push('modals')
    {{-- Create/Edit Language --}}
    <div class="modal fade" id="language-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-3">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">{{ __('back/shop/languages.edit_title') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-10 mx-auto">

                            <div class="mb-3">
                                <label class="form-label">{{ __('back/shop/languages.input_title') }}</label>

                                {{-- Small tab-like pills driven by config('shop.locales') --}}
                                <ul class="nav nav-pills flex-wrap justify-content-end mb-2">
                                    @foreach($locales as $code => $name)
                                        <li class="nav-item me-2 mb-2">
                                            <a class="nav-link @if ($code == current_locale()) active @endif"
                                               data-bs-toggle="pill" href="#lang-title-{{ $code }}">
                                                <img class="me-1" width="18" src="{{ asset('media/flags/' . $code . '.png') }}" />
                                                {{ strtoupper($code) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="tab-content">
                                    @foreach($locales as $code => $name)
                                        <div id="lang-title-{{ $code }}" class="tab-pane fade @if ($code == current_locale()) show active @endif">
                                            <input type="text" class="form-control" id="language-title-{{ $code }}" placeholder="{{ $name }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('back/shop/languages.code_title') }}</label>
                                    <input type="text" class="form-control" id="language-code" placeholder="hr, en, ...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label d-block">{{ __('back/shop/languages.status_title') }}</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="language-status" checked>
                                        <label class="form-check-label" for="language-status">{{ __('back/common.status.active') }}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" id="language-main">
                                <label class="form-check-label" for="language-main">{{ __('back/shop/languages.default_lang') }}</label>
                            </div>

                            <input type="hidden" id="language-id" value="0">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('back/shop/languages.cancel') }}</button>
                    <button class="btn btn-primary" onclick="createLanguage();">{{ __('back/shop/languages.save') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete --}}
    <div class="modal fade" id="delete-language-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ __('back/shop/languages.delete_language') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h5 class="mb-0">{{ __('back/shop/languages.delete_shure') }}</h5>
                    <input type="hidden" id="delete-language-id" value="0">
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-light" data-bs-dismiss="modal">{{ __('back/shop/languages.cancel') }}</button>
                    <button class="btn btn-danger" onclick="confirmDeleteLanguage();">{{ __('back/shop/languages.delete') }}</button>
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

        // Build LANGS array like: [{code:'hr', name:'Hrvatski'}, ...] from PHP
        const LANGS = @json($localesForJs);

        // Expose functions globally for inline handlers
        window.openLangModal = function(item = null) {
            resetLanguageForm();
            if (item) fillLanguageForm(item);
            new bootstrap.Modal(document.getElementById('language-modal')).show();
        };

        window.resetLanguageForm = function() {
            document.getElementById('language-id').value = 0;
            document.getElementById('language-code').value = '';
            document.getElementById('language-status').checked = true;
            document.getElementById('language-main').checked = false;

            LANGS.forEach(l => {
                const el = document.getElementById('language-title-' + l.code);
                if (el) el.value = '';
            });
        };

        window.fillLanguageForm = function(item) {
            document.getElementById('language-id').value = item.id || 0;
            document.getElementById('language-code').value = item.code || '';
            document.getElementById('language-status').checked = !!item.status;
            document.getElementById('language-main').checked = !!item.main;

            LANGS.forEach(l => {
                const el = document.getElementById('language-title-' + l.code);
                if (el && item.title && typeof item.title[l.code] !== 'undefined') {
                    el.value = item.title[l.code];
                }
            });
        };

        window.createLanguage = function() {
            const titles = {};
            LANGS.forEach(l => {
                const el = document.getElementById('language-title-' + l.code);
                titles[l.code] = (el?.value || '').trim();
            });

            const rawCode = (document.getElementById('language-code').value || '').trim().toLowerCase();
            if (!rawCode) return errorToast && errorToast.fire("{{ __('back/shop/languages.code_title') }} {{ __('back/shop/languages.required') }}");

            const item = {
                id: parseInt(document.getElementById('language-id').value || '0', 10),
                title: titles,
                code: rawCode,
                status: !!document.getElementById('language-status').checked,
                main: !!document.getElementById('language-main').checked
            };

            axios.post('/api/v1/settings/languages', { data: item })
            .then(r => {
                if (r.data && r.data.success) location.reload();
                else (errorToast && errorToast.fire(r.data.message || 'Error'));
            })
            .catch(() => errorToast && errorToast.fire('Network error'));
        };

        window.deleteLanguage = function(id) {
            document.getElementById('delete-language-id').value = id;
            new bootstrap.Modal(document.getElementById('delete-language-modal')).show();
        };

        window.confirmDeleteLanguage = function() {
            const id = document.getElementById('delete-language-id').value;
            axios.delete('/api/v1/settings/languages', { data: { id } })
            .then(r => { if (r.data?.success) location.reload(); else (errorToast && errorToast.fire(r.data.message || 'Error')); })
            .catch(() => errorToast && errorToast.fire('Network error'));
        };
    </script>
@endpush
