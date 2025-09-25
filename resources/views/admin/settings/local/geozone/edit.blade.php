@extends('admin.layouts.base-admin')

@section('title', __('back/shop/geozone.edit_page_title'))

@php
    // Locales for multi-lang tabs
    $locales = config('shop.locales', ['hr' => 'Hrvatski', 'en' => 'English']);
    $localesForJs = collect($locales)->map(fn($name,$code)=>['code'=>$code,'name'=>$name])->values();

    // Prepare datasets for JS (Choices)
    $countriesJs = collect($countries ?? [])->map(fn($c)=>[
        'id' => (int)$c['id'],
        'name' => $c['name'],
        'iso2' => $c['iso_code_2'] ?? null
    ])->values();

    $zonesJs = collect($zones ?? [])->map(fn($z)=>[
        'id' => (int)$z['id'],
        'country_id' => (int)$z['country_id'],
        'name' => $z['name'],
        'code' => $z['code']
    ])->values();

    // Legacy state map (id => name) → split into country ids & zone ids
    $stateMap = (array)($item->state ?? []);
    $countryIdSet = collect($countries ?? [])->pluck('id')->map(fn($v)=>(int)$v)->flip();
    $zoneIdSet    = collect($zones ?? [])->pluck('id')->map(fn($v)=>(int)$v)->flip();

    $initialCountries = [];
    $initialZones     = [];
    foreach ($stateMap as $sid => $label) {
        $sid = (int)$sid;
        if ($countryIdSet->has($sid)) { $initialCountries[] = $sid; continue; }
        if ($zoneIdSet->has($sid))    { $initialZones[]     = $sid; }
    }
@endphp

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-1">{{ $item ? __('back/shop/geozone.edit') : __('back/shop/geozone.new') }}</h5>
                        <div class="small text-muted">{{ __('back/shop/geozone.form_help') }}</div>
                    </div>
                    <div>
                        <a href="{{ route('settings.geozones.index') }}" class="btn btn-light">
                            <i class="ti ti-arrow-left"></i> {{ __('back/shop/geozone.back') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Title (multi-lang tabs like currencies) --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('back/shop/geozone.input_title') }}</label>

                        <ul class="nav nav-pills flex-wrap justify-content-end mb-2">
                            @foreach($locales as $code => $name)
                                <li class="nav-item me-2 mb-2">
                                    <a class="nav-link @if ($code == current_locale()) active @endif"
                                       data-bs-toggle="pill" href="#gz-title-{{ $code }}">
                                        <img class="me-1" width="18" src="{{ asset('media/flags/' . $code . '.png') }}" />
                                        {{ strtoupper($code) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach($locales as $code => $name)
                                <div id="gz-title-{{ $code }}" class="tab-pane fade @if ($code == current_locale()) show active @endif">
                                    <input type="text" class="form-control" id="gz-title-input-{{ $code }}" placeholder="{{ $name }}"
                                           value="{{ $item->title->{$code} ?? '' }}">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Description (optional, kept in legacy JSON) --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('back/shop/geozone.description') }}</label>
                        <textarea class="form-control" id="gz-description" rows="2" placeholder="—">{{ $item->description ?? '' }}</textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('back/shop/geozone.countries') }}</label>
                            <select id="gz-countries" class="form-control" multiple data-placeholder="{{ __('back/shop/geozone.select_countries') }}"></select>
                            <div class="form-text">{{ __('back/shop/geozone.countries_help') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('back/shop/geozone.zones') }}</label>
                            <select id="gz-zones" class="form-control" multiple data-placeholder="{{ __('back/shop/geozone.select_zones') }}"></select>
                            <div class="form-text">{{ __('back/shop/geozone.zones_help') }}</div>
                        </div>
                    </div>

                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('back/shop/geozone.sort_order') }}</label>
                            <input type="number" class="form-control" id="gz-sort-order" value="{{ (int)($item->sort_order ?? 0) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label d-block">{{ __('back/shop/geozone.status_title') }}</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="gz-status" @if(!isset($item) || !empty($item->status)) checked @endif>
                                <label class="form-check-label" for="gz-status">{{ __('back/common.status.active') }}</label>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="gz-id" value="{{ (int)($item->id ?? 0) }}">
                </div>

                <div class="card-footer bg-light d-flex justify-content-end gap-2">
                    <a href="{{ route('settings.geozones.index') }}" class="btn btn-light">{{ __('back/shop/geozone.cancel') }}</a>
                    <button class="btn btn-primary" onclick="saveGeozone();">{{ __('back/shop/geozone.save') }}</button>
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

        const LOCALES          = @json($localesForJs ?? []);
        const COUNTRIES        = @json($countriesJs ?? []);
        const ZONES            = @json($zonesJs ?? []);
        const initialCountries = (@json($initialCountries ?? []) || []).map(String);
        const initialZones     = (@json($initialZones ?? []) || []).map(String);

        let choicesCountries = null;
        let choicesZones     = null;
        let firstZoneBuild   = true;

        document.addEventListener('DOMContentLoaded', () => {
            if (!window.Choices) {
                console.warn('Choices.js missing. Ensure it is included before @stack("scripts").');
                return;
            }

            // COUNTRIES — build with preselected
            choicesCountries = new Choices('#gz-countries', {
                removeItemButton: true,
                searchEnabled: true,
                shouldSort: true,
                allowHTML: false,
                placeholder: true,
                placeholderValue: document.getElementById('gz-countries')?.dataset?.placeholder || ''
            });

            const initialCountrySet = new Set(initialCountries);
            choicesCountries.clearChoices();
            choicesCountries.setChoices(
                COUNTRIES.map(c => ({
                    value: String(c.id),
                    label: `${c.name}${c.iso2 ? ' (' + c.iso2 + ')' : ''}`,
                    selected: initialCountrySet.has(String(c.id))
                })),
                'value',
                'label',
                true
            );

            // ZONES — filtered by selected countries, keep selections
            choicesZones = new Choices('#gz-zones', {
                removeItemButton: true,
                searchEnabled: true,
                shouldSort: true,
                allowHTML: false,
                placeholder: true,
                placeholderValue: document.getElementById('gz-zones')?.dataset?.placeholder || ''
            });

            rebuildZones(); // initial render with DB preselected zones

            document.getElementById('gz-countries').addEventListener('change', rebuildZones);
        });

        function getSelectedValues(choicesInstance) {
            if (!choicesInstance) return [];
            const v = choicesInstance.getValue(true);
            return Array.isArray(v) ? v : (v ? [v] : []);
        }

        function rebuildZones() {
            if (!choicesZones) return;

            const selectedCountryIds = getSelectedValues(choicesCountries).map(v => parseInt(v, 10)).filter(Boolean);
            const filtered = selectedCountryIds.length
                ? ZONES.filter(z => selectedCountryIds.includes(parseInt(z.country_id, 10)))
                : ZONES;

            const prevSelected   = new Set(getSelectedValues(choicesZones).map(String));
            const initialZoneSet = new Set(initialZones);

            choicesZones.clearChoices();
            choicesZones.setChoices(
                filtered.map(z => {
                    const idStr = String(z.id);
                    const selected = firstZoneBuild ? initialZoneSet.has(idStr) : prevSelected.has(idStr);
                    return {
                        value: idStr,
                        label: `${z.name}${z.code ? ' (' + z.code + ')' : ''}`,
                        selected
                    };
                }),
                'value',
                'label',
                true
            );

            firstZoneBuild = false;
        }

        // SAVE -> API (no web "update" route)
        window.saveGeozone = function () {
            const titles = {};
            (LOCALES || []).forEach(l => {
                const el = document.getElementById('gz-title-input-' + l.code);
                titles[l.code] = (el?.value || '').trim();
            });

            const selectedCountries = getSelectedValues(choicesCountries).map(v => parseInt(v, 10)).filter(Boolean);
            const selectedZones     = getSelectedValues(choicesZones).map(v => parseInt(v, 10)).filter(Boolean);

            // Legacy "state" shape: object of id => display name (can mix countries/zones)
            const state = {};
            selectedCountries.forEach(cid => { state[cid] = (COUNTRIES.find(c => c.id === cid)?.name || String(cid)); });
            selectedZones.forEach(zid => { state[zid] = (ZONES.find(z => z.id === zid)?.name || String(zid)); });

            const payload = {
                id: parseInt(document.getElementById('gz-id').value || '0', 10),
                title: titles,
                description: (document.getElementById('gz-description')?.value || '').trim() || null,
                status: !!document.getElementById('gz-status')?.checked,
                state
            };

            axios.post('/api/v1/settings/geozones', { data: payload })
            .then(r => {
                if (r.data?.success) location.reload(); // stay simple: just reload after save
                else (window.errorToast && errorToast.fire(r.data.message || 'Error'));
            })
            .catch(() => window.errorToast && errorToast.fire('Network error'));
        };
    </script>
@endpush

