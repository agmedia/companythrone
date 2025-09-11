@php($code = $code ?? 'pickup')
<div class="mt-3">
    <label class="form-label">{{ $label ?? __('back/shop/common.short_desc_input') }} <span class="small text-muted">{{ __('back/shop/common.short_desc_label') }}</span></label>
    <ul class="nav nav-pills flex-wrap justify-content-end mb-2">
        @foreach(($locales ?? config('shop.locales')) as $lc => $lname)
            <li class="nav-item me-2 mb-2">
                <a class="nav-link @if ($lc == current_locale()) active @endif" data-bs-toggle="pill" href="#short-pickup-{{ $lc }}">
                    <img width="18" class="me-1" src="{{ asset('media/flags/'.$lc.'.png') }}"/>{{ strtoupper($lc) }}
                </a>
            </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach(($locales ?? config('shop.locales')) as $lc => $lname)
            <div id="short-pickup-{{ $lc }}" class="tab-pane fade @if ($lc == current_locale()) show active @endif">
                <textarea class="form-control" rows="3" data-config="short_description.{{ $lc }}"></textarea>
            </div>
        @endforeach
    </div>
</div>