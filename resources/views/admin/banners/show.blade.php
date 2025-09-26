@extends('admin.layouts.base-admin')

@php
    /** @var \App\Models\Back\Banners\Banner $banner */
    $t = $banner->translation();
    $feedUrl = route('banners.events.index', $banner);
@endphp

@section('content')
    <div class="row g-3">
        <div class="col-12 col-xl-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">{{ $t?->title ?? 'Banner #'.$banner->id }}</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('banners.edit', $banner) }}" class="btn btn-primary">Edit</a>
                        <a href="{{ route('banners.index') }}" class="btn btn-light">Back</a>
                    </div>
                </div>
                <div class="card-body row g-3">
                    <div class="col-12">
                        @if($url = $banner->getFirstMediaUrl('banner','wide'))
                            <img src="{{ $url }}" class="img-fluid rounded border">
                        @endif
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Status</div>
                        <div><span class="badge @class([
                'bg-outline-secondary' => $banner->status==='draft',
                'bg-success' => $banner->status==='active',
                'bg-secondary' => $banner->status==='archived',
              ])">{{ ucfirst($banner->status) }}</span></div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Clicks</div>
                        <div>{{ number_format($banner->clicks ?? 0) }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">URL</div>
                        <div class="text-break">{{ $t?->url ?? '—' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Slogan</div>
                        <div>{{ $t?->slogan ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FULLCALENDAR + position picker --}}
        <div class="col-12 col-xl-4">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Schedule</h6>
                    <div class="d-flex align-items-center gap-2">
                        <label class="small mb-0">Position</label>
                        <select id="schedule-position" class="form-select form-select-sm">
                            @foreach(range(1,5) as $p) <option value="{{ $p }}">P{{ $p }}</option> @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="banner-calendar"></div>
                    <div class="form-text mt-2">Select range to create (uses Position). Drag/resize to adjust. Click event to delete.</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('banner-calendar');
            const posEl = document.getElementById('schedule-position');

            const calendar = new FullCalendar.Calendar(el, {
                height: 550,
                initialView: 'dayGridMonth',
                selectable: true,
                editable: true,
                eventSources: [{ url: @json($feedUrl), method: 'GET' }],

                select: async (arg) => {
                    await fetch(@json(route('banners.events.store', $banner)), {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': @json(csrf_token()), 'Content-Type': 'application/json'},
                        body: JSON.stringify({ start: arg.startStr, end: arg.endStr, position: parseInt(posEl.value, 10) })
                    });
                    calendar.refetchEvents();
                },

                eventDrop: async (info) => {
                    await fetch(@json(route('banners.events.update', [$banner, 'event' => 'ID'])).replace('ID', info.event.id), {
                        method: 'PATCH',
                        headers: {'X-CSRF-TOKEN': @json(csrf_token()), 'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            start: info.event.startStr,
                            end: info.event.endStr,
                            position: info.event.extendedProps.position ?? parseInt(posEl.value, 10)
                        })
                    });
                },

                eventResize: async (info) => {
                    await fetch(@json(route('banners.events.update', [$banner, 'event' => 'ID'])).replace('ID', info.event.id), {
                        method: 'PATCH',
                        headers: {'X-CSRF-TOKEN': @json(csrf_token()), 'Content-Type': 'application/json'},
                        body: JSON.stringify({ start: info.event.startStr, end: info.event.endStr, position: info.event.extendedProps.position ?? 1 })
                    });
                },

                eventClick: async (info) => {
                    if (!confirm('Delete this slot?')) return;
                    await fetch(@json(route('banners.events.destroy', [$banner, 'event' => 'ID'])).replace('ID', info.event.id), {
                        method: 'DELETE',
                        headers: {'X-CSRF-TOKEN': @json(csrf_token())}
                    });
                    calendar.refetchEvents();
                }
            });

            calendar.render();
        });
    </script>
@endpush
