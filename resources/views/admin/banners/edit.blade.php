@extends('admin.layouts.base-admin')

@php
    /** @var \App\Models\Back\Banners\Banner|null $banner */
    $isEdit = isset($banner) && $banner?->exists;
    $action = $isEdit ? route('banners.update', $banner) : route('banners.store');
    $thumb  = $isEdit ? $banner->getFirstMediaUrl('banner','thumb') : null;

    $currentLocale = function_exists('current_locale') ? current_locale() : app()->getLocale();
@endphp

@section('content')
    <div class="row g-3">
        <div class="col-12 col-lg-10">
            <form action="{{ $action }}" method="POST" class="card" enctype="multipart/form-data">
                @csrf @if($isEdit) @method('PUT') @endif

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ $isEdit ? 'Uredi baner' : 'Kreiraj baner' }}</h5>
                    <a href="{{ route('banners.index') }}" class="btn btn-light">Povratak</a>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                @foreach(['draft'=>'Nacrt','active'=>'Aktivan','archived'=>'Arhiviran'] as $k=>$v)
                                    <option value="{{ $k }}" @selected(old('status', $banner->status ?? 'draft') === $k)>{{ $v }}</option>
                                @endforeach
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- I18N: kartice po jezicima --}}
                        <div class="col-12">
                            {{--  <label class="form-label">Prijevodi</label>
                             <ul class="nav nav-pills flex-wrap justify-content-end mb-2">
                                 @foreach($locales as $code => $name)
                                     <li class="nav-item me-2 mb-2">
                                         <a class="nav-link @if ($code == $currentLocale) active @endif" data-bs-toggle="pill" href="#tr-{{ $code }}">
                                             <img class="me-1" width="18" src="{{ asset('media/flags/' . $code . '.png') }}" />
                                             {{ strtoupper($code) }}
                                         </a>
                                     </li>
                                 @endforeach
                             </ul>
 --}}
                            <div class="tab-content">
                                @foreach($locales as $code => $name)
                                    @php
                                        $t = $isEdit ? $banner->translations->firstWhere('locale', $code) : null;
                                    @endphp
                                    <div id="tr-{{ $code }}" class="tab-pane fade @if ($code == $currentLocale) show active @endif">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Naslov ({{ strtoupper($code) }})</label>
                                                <input type="text" class="form-control @error("tr.$code.title") is-invalid @enderror"
                                                       name="tr[{{ $code }}][title]" value="{{ old("tr.$code.title", $t?->title) }}">
                                                @error("tr.$code.title") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Slogan ({{ strtoupper($code) }})</label>
                                                <input type="text" class="form-control" name="tr[{{ $code }}][slogan]" value="{{ old("tr.$code.slogan", $t?->slogan) }}">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">URL ({{ strtoupper($code) }})</label>
                                                <input type="url" class="form-control @error("tr.$code.url") is-invalid @enderror"
                                                       name="tr[{{ $code }}][url]" value="{{ old("tr.$code.url", $t?->url) }}">
                                                @error("tr.$code.url") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Mediji --}}
                        <div class="col-md-6">
                            <label class="form-label">Slika</label>
                            <input type="file" name="image" class="filepond" accept="image/*">
                            @if($thumb)
                                <div class="form-text mt-2">
                                    <img src="{{ $thumb }}" class="rounded border" style="width:128px;height:64px;object-fit:cover;">
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="remove_image" name="remove_image">
                                    <label class="form-check-label" for="remove_image">Ukloni sliku</label>
                                </div>
                            @endif
                            @error('image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>

                <div class="card-footer d-flex gap-2">
                    <button class="btn btn-primary">{{ $isEdit ? 'Ažuriraj' : 'Spremi' }}</button>
                    <a href="{{ route('banners.index') }}" class="btn btn-secondary">Odustani</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/filepond@^4/dist/filepond.css">
    <link rel="stylesheet" href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css">
@endpush
@push('scripts')
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/filepond@^4/dist/filepond.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.FilePond) {
                FilePond.registerPlugin(FilePondPluginImagePreview);
                document.querySelectorAll('input.filepond').forEach((el) => FilePond.create(el, {
                    allowMultiple:false,
                    credits:false,
                    imagePreviewHeight:120,
                    instantUpload: false, // ide kroz formu pri slanju
                    storeAsFile: true
                }));
            }
        });
    </script>
@endpush
