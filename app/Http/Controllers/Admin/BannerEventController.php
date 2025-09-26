<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\Banner\StoreEventRequest;
use App\Models\Back\Banners\Banner;
use App\Models\Back\Banners\BannerSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BannerEventController extends Controller
{
    // JSON feed (FullCalendar)
    public function index(Banner $banner, Request $request)
    {
        $start = $request->date('start'); // YYYY-MM-DD
        $end   = $request->date('end');

        $events = $banner->schedules()
                         ->when($start, fn($q) => $q->whereDate('end_date', '>=', $start))
                         ->when($end,   fn($q) => $q->whereDate('start_date', '<=', $end))
                         ->get()
                         ->map(function ($s) use ($banner) {
                             $t = $banner->translation();
                             // FullCalendar allDay end je EXCLUSIVE → pošalji +1 dan
                             $endExclusive = $s->end_date?->copy()->addDay()->toDateString();
                             return [
                                 'id'      => $s->id,
                                 'title'   => ($t?->title ?? 'Banner').' (P'.$s->position.')',
                                 'start'   => $s->start_date->toDateString(),
                                 'end'     => $endExclusive,
                                 'allDay'  => true,
                                 'extendedProps' => ['position' => $s->position],
                             ];
                         });

        return response()->json($events);
    }

    // Create (select range)
    public function store(StoreEventRequest $request, Banner $banner)
    {
        [$startDate, $endDate] = $this->datesFromCalendar($request->string('start'), $request->string('end'));
        $event = $banner->schedules()->create([
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'position'   => $request->integer('position', 1),
        ]);

        return response()->json(['ok'=>true,'id'=>$event->id], 201);
    }

    // Drag/resize
    public function update(StoreEventRequest $request, Banner $banner, BannerSchedule $event)
    {
        abort_unless($event->banner_id === $banner->id, 404);

        [$startDate, $endDate] = $this->datesFromCalendar($request->string('start'), $request->string('end'));

        $event->update([
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'position'   => $request->integer('position', $event->position),
        ]);

        return response()->json(['ok'=>true]);
    }

    public function destroy(Banner $banner, BannerSchedule $event)
    {
        abort_unless($event->banner_id === $banner->id, 404);
        $event->delete();

        return response()->json(['ok'=>true]);
    }

    // FullCalendar šalje end EXCLUSIVE → u tablicu spremamo INCLUSIVE
    private function datesFromCalendar(string $start, ?string $end): array
    {
        $s = Carbon::parse($start)->toDateString();
        if ($end) {
            $e = Carbon::parse($end)->subDay()->toDateString();
            if ($e < $s) $e = $s;
        } else {
            $e = $s;
        }
        return [$s, $e];
    }
}
