<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Shared\DailySession;
use App\Models\Back\Catalog\Company;
use Carbon\Carbon;

class CloseDayAndActivateLinks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $today = Carbon::today();

        DailySession::whereDate('day', $today)->chunkById(200, function ($sessions) {
            foreach ($sessions as $s) {
                if ($s->completed_count >= 25) {
                    $s->update(['completed_25' => true]);
                    /** @var Company $c */
                    $c = $s->company;
                    if ($c) $c->forceFill(['is_link_active' => true])->save();
                }
            }
        });
    }
}
