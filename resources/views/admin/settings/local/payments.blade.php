@extends('back.layouts.base-admin')

@section('title', __('back/shop/payments.title'))

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header align-items-center justify-content-between d-flex">
                    <div>
                        <h5 class="mb-1">{{ __('back/shop/payments.title') }}</h5>
                        <div class="small text-muted">{{ __('back/shop/payments.list') }}</div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th style="width:56px;">#</th>
                                <th>{{ __('back/shop/payments.provider') }}</th>
                                <th class="text-center">{{ __('back/shop/payments.status') }}</th>
                                <th class="text-center">{{ __('back/shop/payments.sort_order') }}</th>
                                <th class="text-end" style="width:120px;">{{ __('back/shop/payments.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $i=1; @endphp
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>
                                        @include('back.settings.partials.lang-list-title', ['item' => $item])
                                    </td>
                                    <td class="text-center">
                                        @include('back.settings.partials.list-status', ['item' => $item])
                                    </td>
                                    <td class="text-center">{{ $item->sort_order ?? 0 }}</td>
                                    <td class="text-end">
                                        @include('back.settings.partials.list-action-buttons', ['item' => $item, 'editHandler' => 'openPaymentModal', 'deleteHandler' => 'deletePayment'])
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Provider modals --}}
    @foreach($providers as $code => $def)
        @includeIf('back.settings.shop.payments.modals.' . $code, ['locales' => $locales, 'geozones' => $geo_zones])
    @endforeach
@endsection

@push('scripts')
    <script>
        if (window.axios) {
            window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
        }

        // Build LOCALES safely (no arrow funcs for older PHP/engines)
        @php($__locales = isset($locales) ? $locales : config('shop.locales', ['hr'=>'HR','en'=>'EN']))
        const LOCALES = @json(
  collect($__locales)->map(function ($name, $code) {
    return ['code'=>$code,'name'=>$name];
  })->values()
);

        /** Open a payment modal and populate fields */
        window.openPaymentModal = function(item) {
            const code = item.code;
            const modalEl = document.getElementById('payment-modal-' + code);   // <-- DOM element
            if (!modalEl) return;

            // reset form (if present)
            modalEl.querySelector('form')?.reset();

            // base fields
            const idEl        = modalEl.querySelector('input[name="id"]');
            const sortEl      = modalEl.querySelector('input[name="sort_order"]');
            const statusEl    = modalEl.querySelector('input[name="status"]');
            if (idEl)   idEl.value   = item.id ?? 0;
            if (sortEl) sortEl.value = item.sort_order ?? 0;
            if (statusEl) statusEl.checked = !!item.status;

            // localized title inputs
            LOCALES.forEach(l => {
                const t = modalEl.querySelector('#title-' + code + '-' + l.code);
                if (t) t.value = (item.title && item.title[l.code]) ? item.title[l.code] : '';
            });

            // optional geo zone
            const gz = modalEl.querySelector('select[name="geo_zone"]');
            if (gz) gz.value = (item.geo_zone ?? '');

            // provider config (data-config="key" OR "nested.key.path")
            modalEl.querySelectorAll('[data-config]').forEach(input => {
                const key = input.getAttribute('data-config');
                let val = '';
                if (item.config) {
                    val = key.split('.').reduce((acc, k) => (acc && typeof acc === 'object' && k in acc ? acc[k] : ''), item.config);
                }
                input.value = (val ?? input.getAttribute('data-default') ?? '');
            });

            const bsModal = new bootstrap.Modal(modalEl);   // <-- Bootstrap instance kept separate
            bsModal.show();
        };

        /** Collect & save a payment method */
        window.savePayment = function(code) {
            const modalEl = document.getElementById('payment-modal-' + code);
            if (!modalEl) return;

            const id        = parseInt(modalEl.querySelector('input[name="id"]')?.value || '0', 10);
            const sortOrder = parseInt(modalEl.querySelector('input[name="sort_order"]')?.value || '0', 10);
            const status    = !!modalEl.querySelector('input[name="status"]')?.checked;

            // titles
            const title = {};
            LOCALES.forEach(l => {
                const el = modalEl.querySelector('#title-' + code + '-' + l.code);
                title[l.code] = (el?.value || '').trim();
            });

            // config
            const config = {};
            modalEl.querySelectorAll('[data-config]').forEach(input => {
                const parts = (input.getAttribute('data-config') || '').split('.');
                let cur = config;
                for (let i = 0; i < parts.length; i++) {
                    const p = parts[i];
                    if (i === parts.length - 1) cur[p] = input.value;
                    else cur = (cur[p] = cur[p] && typeof cur[p] === 'object' ? cur[p] : {});
                }
            });

            // optional geo zone
            const gzEl = modalEl.querySelector('select[name="geo_zone"]');
            const geo_zone = gzEl ? (parseInt(gzEl.value || '0', 10) || null) : null;

            const payload = { id, code, title, sort_order: sortOrder, status, config };
            if (geo_zone !== null) payload.geo_zone = geo_zone;

            axios.post('/api/v1/settings/payments', { data: payload })
            .then(r => { if (r.data?.success) location.reload(); else (errorToast && errorToast.fire(r.data.message || 'Error')); })
            .catch(() => errorToast && errorToast.fire('Network error'));
        };

        /** Delete */
        window.deletePayment = function(id) {
            if (!id) return;
            axios.delete('/api/v1/settings/payments', { data: { id } })
            .then(r => { if (r.data?.success) location.reload(); else (errorToast && errorToast.fire(r.data.message || 'Error')); })
            .catch(() => errorToast && errorToast.fire('Network error'));
        };
    </script>

@endpush
