@extends('layouts.app')
@section('title', __('company.add'))
@section('content')

    @include('components.layouts.app..checkout-steps-nav')


<div class="container-xxl py-4">

  <div class="row justify-content-start">
    <div class="col-lg-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h1 class="h2 mb-3">{{ __('company.add') }}</h1>
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
                <label class="form-label" for="weburl">{{ __('company.website') }} *</label>
                <input id="weburl" name="weburl" class="form-control  form-control-lg" placeholder="https:/www.tvrtka.hr" value="{{ old('weburl') }}"  required>
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
                <label for="logo_file" class="form-label">{{ __('company.logo') }}</label>
                <input id="logo_file" type="file" name="logo_file" class="form-control form-control-lg">
            </div>
            <div class="d-flex  mt-3 gap-2">
              <button type="submit" class="btn btn-lg btn-primary ms-auto">{{ __('company.submit') }}  <i class="fi-chevron-right fs-lg ms-1 me-n2"></i></button>

            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

