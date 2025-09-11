@php
    $item = $item ?? collect(['status' => 0]);
@endphp

@if(!empty($item->status))
    <span class="badge bg-success">{{ __('back/common.status.active') }}</span>
@else
    <span class="badge bg-secondary-subtle">{{ __('back/common.status.hidden') }}</span>
@endif
