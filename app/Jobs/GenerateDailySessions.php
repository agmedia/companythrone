<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Back\Catalog\Company;
use App\Models\Shared\DailySession;
use Carbon\Carbon;

class GenerateDailySessions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $today = Carbon::today();

        // 1️⃣ Deaktiviraj sve linkove prije obrade dana
        Company::where('level_id', '>', 1)->update(['is_link_active' => false]);

        Company::query()->where('is_published', true)->chunkById(200, function ($companies) use ($today) {
            foreach ($companies as $company) {
                DailySession::firstOrCreate(
                    ['company_id' => $company->id, 'day' => $today],
                    ['slots_payload' => json_encode($this->buildPayload()), 'completed_count' => 0, 'completed_25' => false]
                );
            }
        });
    }

    private function buildPayload(): array
    {
        // TODO: ubaci realno dohvaćanje linkova po razinama (5x5)
        // placeholder struktura:
        return [
            'slots' => collect(range(1,25))->map(fn($i) => [
                'slot' => $i, 'url' => 'https://example.com', 'company_id' => null, 'level' => 6 - ceil($i/5)
            ])->all()
        ];
    }
}
