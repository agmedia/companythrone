@php($code = $code ?? 'pickup')
<div class="mb-3">
    <label class="form-label">{{ $label ?? __('back/shop/common.input_title') }}</label>

    <ul class="nav nav-pills flex-wrap justify-content-end mb-2">
        @foreach(($locales ?? config('shop.locales')) as $lc => $lname)
            <li class="nav-item me-2 mb-2">
                <a class="nav-link @if ($lc == current_locale()) active @endif"
                   data-bs-toggle="pill"
                   href="#title-tab-{{ $code }}-{{ $lc }}">
                    <img width="18" class="me-1" src="{{ asset('media/flags/'.$lc.'.png') }}"/>
                    {{ strtoupper($lc) }}
                </a>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">
        @foreach(($locales ?? config('shop.locales')) as $lc => $lname)
            <div id="title-tab-{{ $code }}-{{ $lc }}"
                 class="tab-pane fade @if ($lc == current_locale()) show active @endif">
                <input type="text"
                       class="form-control"
                       id="title-{{ $code }}-{{ $lc }}"
                       placeholder="{{ $lname }}">
            </div>
        @endforeach
    </div>
</div>