@php($item = $item ?? 'No Title..')
{{-- --}}
<strong class="text-primary text-capitalize">{{ isset($item->title->{current_locale()}) ? $item->title->{current_locale()} : ($item->title->en ?? '') }}</strong>
@if (isset($item->code))
    <div class="small text-muted">{{ str_replace('_',' ', $item->code) }}</div>
@endif