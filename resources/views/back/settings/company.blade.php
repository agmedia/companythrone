@extends('layouts.app')
@section('title', __('settings.password_title'))

@section('content')

    <div class="container pt-4 pt-sm-5 pb-5 mb-xxl-3">
        <div class="row pt-2 pt-sm-0 pt-lg-2 pb-2 pb-sm-3 pb-md-4 pb-lg-5">


            <!-- Sidebar navigation that turns into offcanvas on screens < 992px wide (lg breakpoint) -->
            <aside class="col-lg-3" style="margin-top: -105px">
                <div class="offcanvas-lg offcanvas-start sticky-lg-top pe-lg-3 pe-xl-4" id="accountSidebar">
                    <div class="d-none d-lg-block" style="height: 105px"></div>

                    <!-- Header -->
                    <div class="offcanvas-header d-lg-block py-3 p-lg-0">
                        <div class="d-flex flex-row flex-lg-column align-items-center align-items-lg-start">

                            <div class="pt-lg-3 ps-3 ps-lg-0">
                                <h6 class="mb-1">{{ auth()->user()->name  }}</h6>
                                <p class="fs-sm mb-0">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#accountSidebar" aria-label="Close"></button>
                    </div>


                    @include('components.layouts.app.usernav')

                    <!-- Body (Navigation) -->

                </div>
            </aside>

            <div class="col-lg-9">



                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h2 class="h5 mb-1">{{ __('company.edit') }}</h2>
                                    <p class="text-muted small mb-3">{{ __('Ažurirajte podatke tvrtke') }}</p>
                                    <form method="post" enctype="multipart/form-data" action="{{ localized_route('companies.store') }}" class="vstack gap-3">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-8">
                                                <label class="form-label " for="name">{{ __('company.name') }} *</label>
                                                <input id="name" name="name" value="{{ old('name') }}" class="form-control  form-control-lg" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label" for="oib">{{ __('company.oib') }} *</label>
                                                <input id="oib" name="oib" value="{{ old('oib') }}" class="form-control  form-control-lg" required>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="email">{{ __('auth.email') }} *</label>
                                                <input id="email" type="email" name="email" class="form-control  form-control-lg" value="{{ old('email') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="website">{{ __('company.website') }} *</label>
                                                <input id="website" name="website" class="form-control  form-control-lg" placeholder="https:/www.tvrtka.hr" value="{{ old('website') }}"  required>
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="street">{{ __('company.street') }}</label>
                                                <input id="street" name="street" class="form-control  form-control-lg" value="{{ old('street') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label" for="street_no">{{ __('company.street_no') }}</label>
                                                <input id="street_no" name="street_no" class="form-control  form-control-lg" value="{{ old('street_no') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label" for="city">{{ __('company.city') }}</label>
                                                <input id="city" name="city" class="form-control  form-control-lg" value="{{ old('city') }}">
                                            </div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="state">{{ __('company.state') }}</label>
                                                <input id="state" name="state" class="form-control  form-control-lg" value="{{ old('state') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label" for="phone">{{ __('company.phone') }}</label>
                                                <input id="phone" name="phone" class="form-control  form-control-lg" value="{{ old('phone') }}">
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label" for="description">{{ __('company.description') }}</label>
                                                <textarea id="description" name="description" class="form-control form-control-lg">{{ old('description') }}</textarea>
                                            </div>

                                            <style>
                                                /* Min visina za editable dio */
                                                .ck-editor__editable[role="textbox"] { min-height: 300px; }
                                            </style>

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


                                        </div>
                                        <div>
                                            <label class="form-label" for="logo">{{ __('company.logo') ?? 'Logo' }}</label>
                                            <input id="logo" type="file" name="logo" class="form-control  form-control-lg">
                                        </div>
                                        <div class="d-flex  mt-3 gap-2">
                                            <button type="submit" class="btn btn-lg btn-primary ">{{ __('settings.save') }} </button>

                                        </div>
                                    </form>
                                </div>
                            </div>



            </div>
        </div>
    </div>
@endsection
