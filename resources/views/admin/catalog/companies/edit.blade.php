{{-- resources/views/admin/companies/upsert.blade.php --}}
@extends('admin.layouts.base-admin')

@php
    /** @var \App\Models\Back\Catalog\Company|null $company */
    $isEdit = isset($company) && $company?->exists;

    $title = $isEdit
        ? ('Uredi tvrtku: ' . ($company->translations->firstWhere('locale', app()->getLocale())->name ?? $company->id))
        : 'Kreiraj tvrtku';

    $action = $isEdit
        ? route('catalog.companies.update', $company)
        : route('catalog.companies.store');

    // Locale tabs (canonical pattern: config('app.locales'))
    $locales = config('app.locales', ['hr' => 'Hrvatski', 'en' => 'English']);
    $current = function_exists('current_locale') ? current_locale() : app()->getLocale();

    // Map translations by locale for easy access
    $trByLocale = [];
    if ($isEdit && $company->relationLoaded('translations')) {
        foreach ($company->translations as $ct) {
            $trByLocale[$ct->locale] = $ct;
        }
    }

    // Helper to format datetime-local value
    $publishedAtValue = old('published_at', isset($company) && $company->published_at ? $company->published_at->format('Y-m-d\TH:i') : null);

    // Provide levels (optional). Expecting $levels = [id => title]
    $levels = $levels ?? [];
@endphp

@section('title', $title)

@section('content')
    <div class="row g-3">
        <div class="col-12 col-lg-12">
            <form action="{{ $action }}" method="POST" class="card" enctype="multipart/form-data">
                @csrf
                @if($isEdit) @method('PUT') @endif

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ $title }}</h5>
                    <a href="{{ route('catalog.companies.index') }}" class="btn btn-light">Natrag</a>
                </div>

                <div class="card-body">
                    {{-- ===== CORE (non-translatable) ===== --}}
                    <div class="row g-3">
                        {{-- level_id --}}
                        <div class="col-md-6">
                            <label class="form-label">Razina</label>
                            <select name="level_id" class="form-select @error('level_id') is-invalid @enderror">
                                <option value="">{{ __('back/common.none') }}</option>
                                @foreach($levels as $id => $label)
                                    <option value="{{ $id }}"
                                        @selected((string)old('level_id', $company->level_id ?? '') === (string)$id)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('level_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- OIB --}}
                        <div class="col-md-6">
                            <label class="form-label">OIB</label>
                            <input type="text" name="oib"
                                   value="{{ old('oib', $company->oib ?? '') }}"
                                   class="form-control @error('oib') is-invalid @enderror"
                                   maxlength="20" autocomplete="off">
                            @error('oib') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{--Weburl --}}
                        <div class="col-md-6">
                            <label class="form-label">Web adresa</label>
                            <input type="text" name="weburl"
                                   value="{{ old('weburl', $company->weburl ?? '') }}"  placeholder="https://www.imewebstranice.hr"
                                   class="form-control @error('weburl') is-invalid @enderror"
                                   autocomplete="off">
                            @error('weburl') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label class="form-label">E-pošta</label>
                            <input type="email" name="email"
                                   value="{{ old('email', $company->email ?? '') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   autocomplete="off">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Street & No --}}
                        <div class="col-md-6">
                            <label class="form-label">Ulica</label>
                            <input type="text" name="street"
                                   value="{{ old('street', $company->street ?? '') }}"
                                   class="form-control @error('street') is-invalid @enderror">
                            @error('street') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Br.</label>
                            <input type="text" name="street_no"
                                   value="{{ old('street_no', $company->street_no ?? '') }}"
                                   class="form-control @error('street_no') is-invalid @enderror">
                            @error('street_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- City / State --}}
                        <div class="col-md-2">
                            <label class="form-label">Grad</label>
                            <input type="text" name="city"
                                   value="{{ old('city', $company->city ?? '') }}"
                                   class="form-control @error('city') is-invalid @enderror">
                            @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Država</label>
                            <input type="text" name="state"
                                   value="{{ old('state', $company->state ?? '') }}"
                                   class="form-control @error('state') is-invalid @enderror">
                            @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Phone --}}
                        <div class="col-md-4">
                            <label class="form-label">Telefon</label>
                            <input type="tel" name="phone"
                                   value="{{ old('phone', $company->phone ?? '') }}"
                                   class="form-control @error('phone') is-invalid @enderror">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Published at --}}
                        <div class="col-md-4">
                            <label class="form-label">Datum i vrijeme objave</label>
                            <input type="datetime-local" name="published_at"
                                   value="{{ $publishedAtValue }}"
                                   class="form-control @error('published_at') is-invalid @enderror">
                            @error('published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Counters --}}
                        <div class="col-md-2">
                            <label class="form-label">Broj preporuka</label>
                            <input type="number" min="0" step="1" name="referrals_count"
                                   value="{{ old('referrals_count', $company->referrals_count ?? 0) }}"
                                   class="form-control @error('referrals_count') is-invalid @enderror">
                            @error('referrals_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Klikovi</label>
                            <input type="number" min="0" step="1" name="clicks"
                                   value="{{ old('clicks', $company->clicks ?? 0) }}"
                                   class="form-control @error('clicks') is-invalid @enderror">
                            @error('clicks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Flags --}}
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_published" value="0">
                                <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1"
                                    @checked(old('is_published', $company->is_published ?? false))>
                                <label class="form-check-label" for="is_published">Objavljeno</label>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_link_active" value="0">
                                <input class="form-check-input" type="checkbox" id="is_link_active" name="is_link_active" value="1"
                                    @checked(old('is_link_active', $company->is_link_active ?? false))>
                                <label class="form-check-label" for="is_link_active">Poveznica aktivna</label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- ===== TRANSLATABLE ===== --}}
                   <div class="mb-3">
                       {{--   <label class="form-label d-flex align-items-center justify-content-between">
                             <span class="me-3">Prijevodi</span>


                            <ul class="nav nav-pills flex-wrap mb-0">
                                @foreach($locales as $code => $name)
                                    <li class="nav-item me-2 mb-2">
                                        <a class="nav-link @if ($code == $current) active @endif"
                                           data-bs-toggle="pill"
                                           href="#company-lang-{{ $code }}">
                                            <img class="me-1" width="18" src="{{ asset('media/flags/' . $code . '.png') }}" />
                                            {{ strtoupper($code) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </label>
                        --}}
                        <div class="tab-content mt-3">
                            @foreach($locales as $code => $name)
                                @php
                                    $tr = $trByLocale[$code] ?? null;
                                @endphp
                                <div id="company-lang-{{ $code }}" class="tab-pane fade @if ($code == $current) show active @endif">
                                    {{-- Name --}}
                                    <div class="mb-3">
                                        <label class="form-label">Naziv </label>
                                        <input type="text"
                                               name="name[{{ $code }}]"
                                               id="company-name-{{ $code }}"
                                               class="form-control @error('name.'.$code) is-invalid @enderror"
                                               value="{{ old('name.'.$code, $tr?->name ?? '') }}"
                                               autocomplete="off">
                                        @error('name.'.$code) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Slug --}}
                                    <div class="mb-3">
                                        <label class="form-label">Slug </label>
                                        <input type="text"
                                               name="slug[{{ $code }}]"
                                               id="company-slug-{{ $code }}"
                                               class="form-control @error('slug.'.$code) is-invalid @enderror"
                                               value="{{ old('slug.'.$code, $tr?->slug ?? '') }}"
                                               autocomplete="off">
                                        <div class="form-text">Automatski se popunjava iz naziva; možeš urediti.</div>
                                        @error('slug.'.$code) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Slogan --}}
                                    <div class="mb-3">
                                        <label class="form-label">Slogan </label>
                                        <input type="text"
                                               name="slogan[{{ $code }}]"
                                               id="company-slogan-{{ $code }}"
                                               class="form-control @error('slogan.'.$code) is-invalid @enderror"
                                               value="{{ old('slogan.'.$code, $tr?->slogan ?? '') }}">
                                        @error('slogan.'.$code) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    {{-- Description --}}
                                    <div class="mb-3">
                                        <label class="form-label">Opis </label>
                                        <textarea
                                            name="description[{{ $code }}]"
                                            id="company-description-{{ $code }}"
                                            rows="4"
                                            class="form-control @error('description.'.$code) is-invalid @enderror"
                                        >{{ old('description.'.$code, $tr?->description ?? '') }}</textarea>
                                        @error('description.'.$code) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>



                    @php
                        $logoThumb  = isset($company) && \Schema::hasTable('media') ? ($company->getFirstMediaUrl('logo', 'thumb') ?? null) : null;
                        $bannerUrl  = isset($company) && \Schema::hasTable('media') ? ($company->getFirstMediaUrl('banner', 'wide') ?? $company->getFirstMediaUrl('banner')) : null;
                    @endphp




                    <hr class="my-4">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Logo</label>
                            <input type="file" name="logo_file" id="logo_file" class="filepond" accept="image/*">
                            @if($logoThumb)
                                <div class="form-text mt-2">
                                    <img src="{{ $logoThumb }}" class="rounded border" style="width:64px;height:64px;object-fit:cover;">
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="remove_logo" name="remove_logo">
                                    <label class="form-check-label" for="remove_logo">{{ __('back/common.actions.remove_image') }}</label>
                                </div>
                            @endif
                            @error('logo_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        {{--   <div class="col-md-8">
                              <label class="form-label">Banner</label>
                              <input type="file" name="banner_file" id="banner_file" class="filepond" accept="image/*">
                              @if($bannerUrl)
                                  <div class="form-text mt-2">
                                      <img src="{{ $bannerUrl }}" class="rounded border" style="width:100%;max-width:320px;height:80px;object-fit:cover;">
                                  </div>
                                  <div class="form-check mt-2">
                                      <input class="form-check-input" type="checkbox" value="1" id="remove_banner" name="remove_banner">
                                      <label class="form-check-label" for="remove_banner">{{ __('back/common.actions.remove_image') }}</label>
                                  </div>
                              @endif
                              @error('banner_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                          </div>
                          --}}
                    </div>



                </div> {{-- /card-body --}}

                <div class="card-footer d-flex gap-2">
                    <button class="btn btn-primary">{{ $isEdit ? 'Ažuriraj' : 'Spremi' }}</button>
                    <a href="{{ route('catalog.companies.index') }}" class="btn btn-secondary">Odustani</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@php
    // For JS: locales array to loop for slug auto-fill
    $localesForJs = array_keys($locales);
@endphp

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/filepond@^4/dist/filepond.css">
    <link rel="stylesheet" href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css">


    <style>
        /* Min visina za editable dio */
        .ck-editor__editable[role="textbox"] { min-height: 300px; }
    </style>
@endpush


@push('scripts')
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            ClassicEditor
                .create(document.querySelector('#company-description-hr'), {
                    toolbar: {
                        items: [
                            'undo','redo','|',
                            'heading','|',
                            'bold','italic','|',
                            'bulletedList','numberedList','|',
                            'blockQuote'
                        ]
                    },
                    removePlugins: [
                        /* link */
                        'Link','AutoLink',
                        /* video (embed) */
                        'MediaEmbed',
                        /* SVE vezano uz slike */
                        'Image','ImageBlock','ImageInline','ImageUpload','ImageInsert',
                        'ImageToolbar','ImageCaption','ImageStyle','AutoImage','PictureEditing',
                        /* cloud / box / finder koji vuku PictureEditing */
                        'CKBox','CKBoxToolbar','CKFinder','EasyImage','CloudServices'
                    ]
                })
                .then(editor => {
                    editor.ui.view.editable.element.style.minHeight = '200px';
                })
                .catch(err => console.error('CKEditor init error:', err));
        });
    </script>
    <script src="https://unpkg.com/filepond@^4/dist/filepond.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.FilePond) {
                FilePond.registerPlugin(FilePondPluginImagePreview);
                document.querySelectorAll('input.filepond').forEach((el) => {
                    FilePond.create(el, {
                        allowMultiple: false,
                        credits: false,
                        imagePreviewHeight: 120,
                        instantUpload: false, // submitom ide kroz formu
                        storeAsFile: true
                    });
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Simple slugify
            function slugify(str) {
                return (str || '')
                    .toString()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '')
                    .substring(0, 120);
            }

            // Auto-fill slug from name (per locale) if slug is empty or matches previous auto
            const locales = @json($localesForJs);

            locales.forEach(code => {
                const nameEl = document.getElementById(`company-name-${code}`);
                const slugEl = document.getElementById(`company-slug-${code}`);
                if (!nameEl || !slugEl) return;

                const maybeFill = () => {
                    const proposed = slugify(nameEl.value);
                    // Fill if empty or if it looks like an auto-slug
                    if (!slugEl.value || slugEl.dataset.autofilled === '1') {
                        slugEl.value = proposed;
                        slugEl.dataset.autofilled = '1';
                    }
                };

                nameEl.addEventListener('input', maybeFill);
                nameEl.addEventListener('blur', maybeFill);
                // If slug is manually changed, stop auto-fill
                slugEl.addEventListener('input', () => { slugEl.dataset.autofilled = '0'; });
            });
        });
    </script>
@endpush
