<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Back\Catalog\Company;
use App\Models\Back\Catalog\Level;
use Illuminate\Support\Facades\DB;

class RotateLevels implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Primjer „cikliranja“ levela (simple round-robin)
        DB::transaction(function () {
            $max = Level::max('number') ?? 5;

            Company::query()->orderBy('id')->chunkById(500, function ($batch) use ($max) {
                foreach ($batch as $c) {
                    $next = ($c->level?->number ?? 1) + 1;
                    if ($next > $max) $next = 1;
                    $c->level_id = Level::where('number', $next)->value('id');
                    $c->save();
                }
            });

            Level::query()->update(['rotated_at' => now()]);
        });
    }
}
