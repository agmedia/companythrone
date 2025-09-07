<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Company;
use App\Models\Shared\DailySession;
use App\Models\Shared\Click;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ClickRedirectController extends Controller
{
    /**
     * Potpisani redirect: /r/{from}/{to}/{slot}
     * - anti-spam rate limit po IP/targetu
     * - logira klik u clicks
     * - uvećava completed_count za {from} i po potrebi postavlja completed_25
     * - preusmjerava na website_url ili profil tvrtke
     */
    public function go(Request $request, int $from, int $to, int $slot)
    {
        // Anti-spam: max 60 klikova/IP na istu target tvrtku u 1h
        $key = "click:{$to}:" . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 60)) {
            abort(429, 'Too many clicks, try later.');
        }
        RateLimiter::hit($key, 3600);

        $target = Company::findOrFail($to);

        // Zapiši klik
        Click::create([
            'company_id'      => $target->id,                 // target
            'from_company_id' => $from ?: null,               // izvor (može biti null/0)
            'day'             => Carbon::today(),
            'slot'            => (int) $slot,
            'link_url'        => $target->website_url ?? route('companies.show', $target),
            'ip'              => $request->ip(),
            'user_agent'      => Str::limit((string) $request->userAgent(), 255),
        ]);

        // Uvećaj dnevni progress izvora
        if ($from) {
            $session = DailySession::firstOrCreate(
                ['company_id' => $from, 'day' => Carbon::today()],
                ['completed_count' => 0, 'completed_25' => false, 'slots_payload' => json_encode(['slots' => []])]
            );

            if ($session->completed_count < 25) {
                $session->increment('completed_count');

                if ($session->completed_count >= 25 && ! $session->completed_25) {
                    $session->forceFill(['completed_25' => true])->save();
                    // Ako koristiš evente: DailySessionCompleted::dispatch($session->company, Carbon::today());
                }
            }
        }

        // Redirect na web tvrtke (ili profil ako nema URL kolone/podatka)
        $toUrl = $target->website_url ?? route('companies.show', $target);
        return redirect()->away($toUrl);
    }
}
