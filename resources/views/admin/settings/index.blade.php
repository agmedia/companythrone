@extends('admin.layouts.base-admin')

@section('title', 'Settings')

@section('content')
    <div class="row g-3">
        <div class="col-12 col-lg-10">
            <form action="{{ route('app.settings.update') }}" method="POST" enctype="multipart/form-data" class="card">
                @csrf

                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Settings</h5>
                    <button class="btn btn-primary">{{ __('back/common.actions.save') ?? 'Save' }}</button>
                </div>

                <div class="card-body">
                    {{-- Tabs for groups --}}
                    @php
                        $groups = $groups ?? [];
                        $fields = $fields ?? [];
                        $values = $values ?? [];
                        $current = request('tab', array_key_first($groups) ?? 'site');
                        $locales = config('app.locales', ['hr'=>'Hrvatski', 'en'=>'English']);
                        $currentLocale = function_exists('current_locale') ? current_locale() : app()->getLocale();
                    @endphp

                    <ul class="nav nav-tabs mb-3">
                        @foreach($groups as $code => $meta)
                            <li class="nav-item">
                                <a class="nav-link @if($current === $code) active @endif" data-bs-toggle="tab" href="#settings-{{ $code }}">
                                    @if(!empty($meta['icon'])) <i class="{{ $meta['icon'] }} me-1"></i> @endif
                                    {{ $meta['label'] ?? ucfirst($code) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content">
                        @foreach($groups as $code => $meta)
                            <div id="settings-{{ $code }}" class="tab-pane fade @if($current === $code) show active @endif">
                                <div class="row g-3">
                                    @foreach(($fields[$code] ?? []) as $key => $def)
                                        @php
                                            $type = $def['type'] ?? 'text';
                                            $label = $def['label'] ?? ucfirst($key);
                                            $col = $def['col'] ?? '12';
                                            $val = $values[$code][$key] ?? ($def['default'] ?? null);
                                        @endphp

                                        @if(in_array($type, ['i18n_text','i18n_textarea']))
                                            <div class="col-12">
                                                <label class="form-label">{{ $label }}</label>

                                                <ul class="nav nav-pills flex-wrap justify-content-end mb-2">
                                                    @foreach($locales as $lc => $lname)
                                                        <li class="nav-item me-2 mb-2">
                                                            <a class="nav-link @if ($lc == $currentLocale) active @endif"
                                                               data-bs-toggle="pill" href="#{{ $code }}-{{ $key }}-{{ $lc }}">
                                                                <img class="me-1" width="18" src="{{ asset('media/flags/' . $lc . '.png') }}" />
                                                                {{ strtoupper($lc) }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>

                                                <div class="tab-content">
                                                    @foreach($locales as $lc => $lname)
                                                        <div id="{{ $code }}-{{ $key }}-{{ $lc }}"
                                                             class="tab-pane fade @if ($lc == $currentLocale) show active @endif">
                                                            @if($type === 'i18n_textarea')
                                                                <textarea name="settings[{{ $code }}][{{ $key }}][{{ $lc }}]"
                                                                          rows="3" class="form-control">{{ old("settings.$code.$key.$lc", $val[$lc] ?? '') }}</textarea>
                                                            @else
                                                                <input type="text" class="form-control"
                                                                       name="settings[{{ $code }}][{{ $key }}][{{ $lc }}]"
                                                                       value="{{ old("settings.$code.$key.$lc", $val[$lc] ?? '') }}">
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @elseif($type === 'boolean')
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-md-{{ $col }} d-flex align-items-end mt-4">
                                                        <div class="form-check form-switch">
                                                            <input type="hidden" name="settings[{{ $code }}][{{ $key }}]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                   name="settings[{{ $code }}][{{ $key }}]" value="1"
                                                                   id="{{ $code }}-{{ $key }}" @checked(old("settings.$code.$key", (bool)$val))>
                                                            <label class="form-check-label" for="{{ $code }}-{{ $key }}">{{ $label }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($type === 'number')
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-md-{{ $col }}">
                                                        <label class="form-label">{{ $label }}</label>
                                                        <input type="number" class="form-control"
                                                               name="settings[{{ $code }}][{{ $key }}]"
                                                               value="{{ old("settings.$code.$key", $val) }}"
                                                               @if(isset($def['min'])) min="{{ $def['min'] }}" @endif
                                                               @if(isset($def['max'])) max="{{ $def['max'] }}" @endif
                                                               @if(isset($def['step'])) step="{{ $def['step'] }}" @endif>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($type === 'decimal')
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-md-{{ $col }}">
                                                        <label class="form-label">{{ $label }}</label>
                                                        <input type="number" class="form-control"
                                                               name="settings[{{ $code }}][{{ $key }}]"
                                                               value="{{ old("settings.$code.$key", $val) }}"
                                                               step="{{ $def['step'] ?? '0.01' }}"
                                                               @if(isset($def['min'])) min="{{ $def['min'] }}" @endif
                                                               @if(isset($def['max'])) max="{{ $def['max'] }}" @endif>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($type === 'email')
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-md-{{ $col }}">
                                                        <label class="form-label">{{ $label }}</label>
                                                        <input type="email" class="form-control"
                                                               name="settings[{{ $code }}][{{ $key }}]"
                                                               value="{{ old("settings.$code.$key", $val) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($type === 'textarea')
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-md-{{ $col }}">
                                                        <label class="form-label">{{ $label }}</label>
                                                        <textarea name="settings[{{ $code }}][{{ $key }}]" class="form-control" rows="3">{{ old("settings.$code.$key", $val) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($type === 'file-input')
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-md-{{ $col }}">
                                                        <label class="form-label">{{ $label }}</label>
                                                        <input type="file" class="form-control"
                                                               name="settings[{{ $code }}][{{ $key }}]">

                                                        @if(!empty($val))
                                                            <div class="mt-2">
                                                                <img src="{{ asset('storage/' . $val) }}" style="max-height: 150px;">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                        @else
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-md-{{ $col }}">
                                                        <label class="form-label">{{ $label }}</label>
                                                        <input type="text" class="form-control"
                                                               name="settings[{{ $code }}][{{ $key }}]"
                                                               value="{{ old("settings.$code.$key", $val) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

                <div class="card-footer d-flex gap-2">
                    <button class="btn btn-primary">{{ __('back/common.actions.save') ?? 'Save' }}</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
