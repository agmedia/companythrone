@extends('layouts.app')
@section('title', __('Moja tvrtka'))
@section('content')
    <div class="container py-4">
        <div class="row">
            @include('front.account._sidebar')

            <div class="col-lg-9">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="h4 mb-3">{{ __('Podaci o tvrtki') }}</h1>

                        @include('components.layouts.app.session')

                        <form method="post"
                              enctype="multipart/form-data"
                              action="{{ localized_route('account.company.update') }}"
                              class="vstack gap-3">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label for="name" class="form-label">{{ __('company.name') }} *</label>
                                    <input id="name" name="name" class="form-control form-control-lg"
                                           value="{{ old('name', $company->t_name ?? '') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="oib" class="form-label">{{ __('company.oib') }} *</label>
                                    <input id="oib" name="oib" class="form-control form-control-lg"
                                           value="{{ old('oib', $company->oib ?? '') }}" required>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">{{ __('auth.email') }} *</label>
                                    <input id="email" type="email" name="email" class="form-control form-control-lg"
                                           value="{{ old('email', $company->email ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="weburl" class="form-label">{{ __('company.weburl') }} *</label>
                                    <input id="weburl" name="weburl" class="form-control form-control-lg"
                                           value="{{ old('weburl', $company->weburl ?? '') }}" placeholder="https://www.tvrtka.hr" required>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="street" class="form-label">{{ __('company.street') }}</label>
                                    <input id="street" name="street" class="form-control form-control-lg"
                                           value="{{ old('street', $company->street ?? '') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="street_no" class="form-label">{{ __('company.street_no') }}</label>
                                    <input id="street_no" name="street_no" class="form-control form-control-lg"
                                           value="{{ old('street_no', $company->street_no ?? '') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="city" class="form-label">{{ __('company.city') }}</label>
                                    <input id="city" name="city" class="form-control form-control-lg"
                                           value="{{ old('city', $company->city ?? '') }}">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="state" class="form-label">{{ __('company.state') }}</label>
                                    <input id="state" name="state" class="form-control form-control-lg"
                                           value="{{ old('state', $company->state ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">{{ __('company.phone') }}</label>
                                    <input id="phone" name="phone" class="form-control form-control-lg"
                                           value="{{ old('phone', $company->phone ?? '') }}">
                                </div>
                            </div>

                            <div>
                                <label for="keywords" class="form-label">Ključne riječi <small>Ključne riječi bi trebale biti odvojene zarezom</small></label>
                                <input id="keywords" name="keywords" class="form-control form-control-lg"
                                       value="{{ old('keywords', $company->keywords ?? '') }}">
                                <div class="invalid-feedback d-block" id="keywords-error">
                                    @error('keywords')
                                    {{ $message }}
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="description" class="form-label">{{ __('company.description') }}</label>
                                <textarea id="description" name="description" class="form-control form-control-lg">{!! old('description', $company->t_desc ?? '') !!} </textarea>
                            </div>

                            <style>
                                /* Min visina za editable dio */
                                .ck-editor__editable[role="textbox"] { min-height: 300px; }
                            </style>

                            <div>
                                <label for="logo_file" class="form-label">{{ __('company.logo') }}</label>
                                <input id="logo_file" type="file" name="logo_file" class="form-control form-control-lg">

                                @if(!empty($company->getFirstMediaUrl('logo')))
                                    <div class="mt-2 row align-items-center">
                                        <div class="col-md-3">
                                            <img src="{{ $company->getFirstMediaUrl('logo') }}" class="img-thumbnail" alt="Logo" height="60">
                                        </div>
                                        <div class="col-md-9 d-flex align-items-center">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1" id="remove_logo" name="remove_logo">
                                                <label class="form-check-label" for="remove_logo">
                                                    {{ __('Ukloni postojeći logo') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>



                            <div class="d-flex mt-3 gap-2">
                                <button type="submit" class="btn btn-lg btn-primary ms-auto">
                                    {{ __('Spremi') }}
                                    <i class="fi-chevron-right fs-lg ms-1 me-n2"></i>
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            ClassicEditor
            .create(document.querySelector('#description'), {
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[action="{{ localized_route('account.company.update') }}"]');
            const keywordsInput = document.getElementById('keywords');
            const errorBox = document.getElementById('keywords-error');

            if (!form || !keywordsInput) return;

            const validateKeywords = () => {
                const raw = keywordsInput.value.trim();
                errorBox.textContent = '';
                keywordsInput.classList.remove('is-invalid');

                if (!raw) return true; // prazno je ok

                // Razdvoji po zarezima, makni višestruke razmake i prazne dijelove
                const parts = raw.split(',').map(k => k.trim()).filter(k => k.length > 0);

                if (parts.length > 5) {
                    errorBox.textContent = 'Maksimalno 5 ključnih riječi odvojenih zarezom.';
                    keywordsInput.classList.add('is-invalid');
                    return false;
                }

                // Provjeri svaku riječ, ali zarez ne smije biti unutar nje
                for (const kw of parts) {
                    if (kw.length > 30) {
                        errorBox.textContent = 'Svaka ključna riječ može imati najviše 30 znakova.';
                        keywordsInput.classList.add('is-invalid');
                        return false;
                    }
                    // Dozvoli HR dijakritike, brojeve, razmake, crtice i podvlake — zarez se ne provjerava jer je već separator
                    if (!/^[A-Za-z0-9ĆČŽŠĐćčžšđ\s\-_]+$/.test(kw)) {
                        errorBox.textContent = 'Ključne riječi mogu sadržavati samo slova, brojeve, crtice i razmake (bez točaka i kosih crta).';
                        keywordsInput.classList.add('is-invalid');
                        return false;
                    }
                }

                keywordsInput.value = parts.join(', ');
                return true;
            };

            // Live validacija (ali ne forsira error dok korisnik tipka zarez)
            keywordsInput.addEventListener('input', () => {
                // Ako posljednji znak nije zarez, validiraj
                const val = keywordsInput.value.trim();
                if (!val.endsWith(',')) validateKeywords();
                else {
                    // Ako korisnik piše novu riječ, resetaj error
                    errorBox.textContent = '';
                    keywordsInput.classList.remove('is-invalid');
                }
            });

            // Validacija pri submitu
            form.addEventListener('submit', function (e) {
                if (!validateKeywords()) {
                    e.preventDefault();
                    keywordsInput.focus();
                }
            });
        });
    </script>

@endpush