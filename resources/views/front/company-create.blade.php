@extends('layouts.app')
@section('title', __('company.add'))
@section('content')

    {{-- ✅ ispravljeno: jedna točka --}}
    @include('components.layouts.app.checkout-steps-nav')

    <div class="container-xxl py-4">

        <div class="row justify-content-start">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h1 class="h2 mb-3">{{ __('company.add') }}</h1>

                        {{-- ✅ Globalni prikaz grešaka --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $msg)
                                        <li>{{ $msg }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- ⛳️ Po želji ukloni novalidate ako želiš HTML5 validaciju prije submit-a --}}
                        <form method="post" enctype="multipart/form-data" action="{{ localized_route('companies.store') }}" class="vstack gap-3" novalidate>
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label" for="name">{{ __('company.name') }} <span class="text-danger">*</span></label>
                                    <input id="name" name="name"
                                           value="{{ old('name', $company->t_name ?? $company->name ?? '') }}"
                                           class="form-control form-control-lg @error('name') is-invalid @enderror" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label" for="oib">{{ __('company.oib') }}
                                        {{-- ⚠️ u kontroleru je oib nullable; makni * ako nije obavezno --}}
                                        {{-- <span class="text-danger">*</span> --}}
                                    </label>
                                    <input id="oib" name="oib"
                                           value="{{ old('oib', $company->oib ?? '') }}"
                                           class="form-control form-control-lg @error('oib') is-invalid @enderror"  required>
                                    @error('oib')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="email">{{ __('auth.email') }} <span class="text-danger">*</span></label>
                                    <input id="email" type="email" name="email"
                                           class="form-control form-control-lg @error('email') is-invalid @enderror"
                                           value="{{ old('email', $company->email ?? '') }}" required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="weburl">{{ __('company.website') }} <span class="text-danger">*</span></label>
                                    <input id="weburl" name="weburl" type="url"
                                           class="form-control form-control-lg @error('weburl') is-invalid @enderror"
                                           placeholder="https://www.tvrtka.hr"
                                           value="{{ old('weburl', $company->weburl ?? '') }}" required>
                                    @error('weburl')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="street">{{ __('company.street') }} <span class="text-danger">*</span></label>
                                    <input id="street" name="street"
                                           class="form-control form-control-lg @error('street') is-invalid @enderror"
                                           value="{{ old('street', $company->street ?? '') }}" required>
                                    @error('street')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label" for="street_no">{{ __('company.street_no') }} <span class="text-danger">*</span></label>
                                    <input id="street_no" name="street_no"
                                           class="form-control form-control-lg @error('street_no') is-invalid @enderror"
                                           value="{{ old('street_no', $company->street_no ?? '') }}" required>
                                    @error('street_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label" for="city">{{ __('company.city') }} <span class="text-danger">*</span></label>
                                    <input id="city" name="city"
                                           class="form-control form-control-lg @error('city') is-invalid @enderror"
                                           value="{{ old('city', $company->city ?? '') }}" required>
                                    @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="state">{{ __('company.state') }}</label>
                                    <input id="state" name="state"
                                           class="form-control form-control-lg @error('state') is-invalid @enderror"
                                           value="{{ old('state', $company->state ?? '') }}">
                                    @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label" for="phone">{{ __('company.phone') }}</label>
                                    <input id="phone" name="phone"
                                           class="form-control form-control-lg @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $company->phone ?? '') }}">
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label" for="description">{{ __('company.description') }}</label>
                                    <textarea id="description" name="description"
                                              class="form-control form-control-lg @error('description') is-invalid @enderror">{{ old('description', $company->t_description ?? $company->description ?? '') }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <style>
                                    .ck-editor__editable[role="textbox"] { min-height: 300px; }
                                </style>

                                <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        ClassicEditor.create(document.querySelector('#description'), {
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
                                                'Link','AutoLink',
                                                'MediaEmbed',
                                                'Image','ImageBlock','ImageInline','ImageUpload','ImageInsert',
                                                'ImageToolbar','ImageCaption','ImageStyle','AutoImage','PictureEditing',
                                                'CKBox','CKBoxToolbar','CKFinder','EasyImage','CloudServices'
                                            ]
                                        })
                                            .then(editor => { editor.ui.view.editable.element.style.minHeight = '200px'; })
                                            .catch(err => console.error('CKEditor init error:', err));
                                    });
                                </script>
                            </div>

                            <div>
                                <label for="logo_file" class="form-label">{{ __('company.logo') }}</label>
                                <input id="logo_file" type="file" name="logo_file"
                                       class="form-control form-control-lg @error('logo_file') is-invalid @enderror">
                                @error('logo_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex mt-3 gap-2">
                                <button type="submit" class="btn btn-lg btn-primary ms-auto">
                                    {{ __('company.submit') }}
                                    <i class="fi-chevron-right fs-lg ms-1 me-n2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const checkFields = ['email', 'weburl'];
                const debounce = (fn, delay = 600) => {
                    let timer; return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); };
                };

                const showStatus = (input, exists) => {
                    let msg = input.parentElement.querySelector('.input-hint');
                    if (!msg) {
                        msg = document.createElement('div');
                        msg.className = 'input-hint small mt-1';
                        input.parentElement.appendChild(msg);
                    }
                    if (exists) {
                        input.classList.add('is-invalid');
                        msg.classList.remove('text-success');
                        msg.classList.add('text-danger');
                        msg.textContent = (input.name === 'email')
                            ? "{{ __('Ova email adresa je već registrirana.') }}"
                            : "{{ __('Ova web stranica već postoji u sustavu.') }}";
                    } else {
                        input.classList.remove('is-invalid');
                        msg.classList.remove('text-danger');
                        msg.classList.add('text-success');
                        msg.textContent = (input.name === 'email')
                            ? "{{ __('Email adresa je slobodna.') }}"
                            : "{{ __('Web adresa je slobodna.') }}";
                    }
                };

                const checkUnique = debounce((input) => {
                    const value = input.value.trim();
                    if (value.length < 3) return;

                    fetch("{{ route('companies.checkUnique') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ field: input.name, value })
                    })
                        .then(r => r.json())
                        .then(data => showStatus(input, data.exists))
                        .catch(console.error);
                });

                checkFields.forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (!input) return;
                    input.addEventListener('input', () => checkUnique(input));
                });
            });
        </script>
    @endpush
@endsection
