@php
    $groups = [
      'categories' => __('back/categories.tabs.products'),

      'pages'    => __('back/categories.tabs.pages'),

    ];
    $currentGroup = old('group', $category->group ?? request('group','categories'));
    $t = $category->translation();

    $imgThumb   = \Schema::hasTable('media') ? $category?->getFirstMediaUrl('image', 'thumb') : null;
    $iconThumb  = \Schema::hasTable('media') ? $category?->getFirstMediaUrl('icon', 'thumb')  : null;
    $bannerUrl  = \Schema::hasTable('media') ? $category?->getFirstMediaUrl('banner')        : null;


@endphp



<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('back/categories.form.group') }}</label>
        <select name="group" class="form-select">
            @foreach($groups as $val => $label)
                <option value="{{ $val }}" @selected($currentGroup === $val)>{{ $label }}</option>
            @endforeach
        </select>
        @error('group') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-8">
        <label class="form-label">{{ __('back/categories.form.parent') }}</label>
        <select name="parent_id" class="form-select">
            <option value="">{{ __('back/common.none') }}</option>
            @foreach($parents as $p)
                @php $pt = $p->translation(); @endphp
                <option value="{{ $p->id }}" @selected(old('parent_id', $category->parent_id ?? null) == $p->id)>
                    {{ $pt?->name ?? '—' }}
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('back/categories.form.parent_hint') }}</div>
    </div>

    {{-- Translated fields --}}
    <div class="col-md-6">
        <label class="form-label">{{ __('back/categories.form.title') }}</label>
        <input type="text" name="name" value="{{ old('name', $t->name ?? '') }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label">{{ __('back/categories.form.slug') }}</label>
        <input type="text" name="slug" value="{{ old('slug', $t?->slug ?? '') }}" class="form-control">
       {{--  <div class="form-text">{{ __('back/categories.form.auto_slug_hint') }}</div>--}}
    </div>

    <div class="col-12">
        <label class="form-label">{{ __('back/categories.form.description') }}</label>
        <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $t?->description ?? '') }}</textarea>
    </div>

    {{-- MEDIA (FilePond)
    <div class="col-md-4">
        <label class="form-label">{{ __('back/categories.form.image') }}</label>
        <input type="file" name="image_file" id="image_file" class="filepond" accept="image/*">
        @if($imgThumb)
            <div class="form-text mt-2">
                <img src="{{ $imgThumb }}" class="rounded border" style="width:64px;height:64px;object-fit:cover;">
            </div>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" value="1" id="remove_image" name="remove_image">
                <label class="form-check-label" for="remove_image">{{ __('back/common.actions.remove_image') }}</label>
            </div>
        @endif
        @error('image_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">{{ __('back/categories.form.icon') }}</label>
        <input type="file" name="icon_file" id="icon_file" class="filepond" accept="image/*">
        @if($iconThumb)
            <div class="form-text mt-2">
                <img src="{{ $iconThumb }}" class="rounded border" style="width:48px;height:48px;object-fit:cover;">
            </div>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" value="1" id="remove_icon" name="remove_icon">
                <label class="form-check-label" for="remove_icon">{{ __('back/common.actions.remove_image') }}</label>
            </div>
        @endif
        @error('icon_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">{{ __('back/categories.form.banner') }}</label>
        <input type="file" name="banner_file" id="banner_file" class="filepond" accept="image/*">
        @if($bannerUrl)
            <div class="form-text mt-2">
                <img src="{{ $bannerUrl }}" class="rounded border" style="width:100%;max-width:220px;height:66px;object-fit:cover;">
            </div>
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" value="1" id="remove_banner" name="remove_banner">
                <label class="form-check-label" for="remove_banner">{{ __('back/common.actions.remove_image') }}</label>
            </div>
        @endif
        @error('banner_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div> --}}

    {{-- Flags / ordering --}}
    <div class="col-md-2">
        <label class="form-label">{{ __('back/categories.form.sort_order') }}</label>
        <input type="number" name="position" value="{{ old('position', $category->position ?? 0) }}" class="form-control">
    </div>
    <div class="col-md-12 d-flex align-items-end">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" value="1" name="is_active" id="is_active"
                    @checked(old('is_active', ($category->is_active ?? true)))>
            <label class="form-check-label" for="is_active">{{ __('back/categories.form.is_active') }}</label>
        </div>
    </div>
</div>

@push('styles')
    {{-- FilePond CSS (CDN) --}}
    <link rel="stylesheet" href="https://unpkg.com/filepond@^4/dist/filepond.css">
    <link rel="stylesheet" href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css">
    <style>
        /* Min visina za editable dio */
        .ck-editor__editable[role="textbox"] { min-height: 300px; }
    </style>
@endpush

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
    {{-- FilePond JS (CDN)
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond@^4/dist/filepond.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.FilePond) {
                FilePond.registerPlugin(FilePondPluginImagePreview);

                const opts = {
                    allowMultiple: false,
                    credits: false,
                    imagePreviewHeight: 120,
                    instantUpload: false, // submitom ide kroz formu
                    storeAsFile: true
                };

                document.querySelectorAll('input.filepond').forEach((el) => {
                    FilePond.create(el, opts);
                });
            }
        });
    </script>  --}}
@endpush
