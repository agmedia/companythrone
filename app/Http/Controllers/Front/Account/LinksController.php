<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\Controller;
use App\Mail\ReferralInvitationMail;
use App\Models\Back\Catalog\Company;
use App\Models\Shared\Click;
use App\Models\Shared\DailySession;
use App\Models\Shared\ReferralLink;
use App\Services\Settings\SettingsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LinksController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $userId  = $user->id;
        $company = $user->company; // može biti null

        if (!$company) {
            return redirect()->route('front.account.dashboard');
        }

        // Referrals (list + count)
        $referrals     = ReferralLink::query()->where('user_id', $userId)->latest()->get();
        $referralCount = $referrals->count();

        // Paginirani popis linkova + danas poslano
        $links = ReferralLink::query()->where('user_id', $userId)->latest()->paginate(10);
        $today = ReferralLink::query()
            ->where('user_id', $userId)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        // Postavke
        $sm         = new SettingsManager();
        $limit      = (int) $sm->get('company', 'auth_clicks_required');
        $ref_limit  = (int) $sm->get('company', 'auth_referrals_required');

        // Osiguraj dnevnu sesiju
        $session = DailySession::firstOrCreate(
            ['company_id' => $company->id, 'day' => now()->toDateString()],
            ['slots_payload' => json_encode([])]
        );

        // Već posjećene kompanije danas (po company_id)
        $visitedCompanyIds = Click::query()
            ->where('from_company_id', $company->id)
            ->whereDate('day', now()->toDateString())
            ->pluck('company_id');

        // Korišteni slotovi danas (lista intova) — za Blade koji uspoređuje po slotu
        $usedSlots = Click::query()
            ->where('from_company_id', $company->id)
            ->whereDate('day', now()->toDateString())
            ->pluck('slot');

        // Broj današnjih klikova (po day)
        $todayClicks = $usedSlots->count();

        // Preostalo do limita (spriječi negativan limit())
        $remaining = max(0, $limit - $visitedCompanyIds->count());

        // Targeti: aktivne, objavljene, nisu već posjećene danas, ne uključuj vlastitu
        $targets = Company::query()
            ->where('id', '!=', $company->id)
            ->where('is_published', 1)
            ->where('is_link_active', 1)
            ->whereNotIn('id', $visitedCompanyIds)
            // ->inRandomOrder()
            ->limit($remaining)
            ->get(['id', 'weburl', 't_name']);

        return view('front.account.links', [
            'links'            => $links,
            'todayLinks'       => $today,
            'limitPerDay'      => $limit,
            'todayClicks'      => $todayClicks,
            'session'          => $session,          // može biti null → u bladeu provjeri s @isset
            'usedSlots'        => $usedSlots,        // <= Blade očekuje slotove (brojeve)
            'targets'          => $targets,
            'referrals'        => $referrals,
            'referralCount'    => $referralCount,
            'referralRequired' => $ref_limit,
        ]);
    }

    public function click(Request $request)
    {
        $request->validate([
            // više NE primamo slot od klijenta — računamo na serveru
            'target_company_id' => 'required|integer|exists:companies,id',
            'url'               => 'nullable|string|max:2048',
        ]);

        $user    = auth()->user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('front.account.dashboard');
        }

        // Postavke (možeš i preko SettingsManagera; koristim postojeću tvoju helper funkciju)
        $limit     = (int) app_settings()->clicksRequired();
        $ref_limit = (int) app_settings()->referralsRequired();

        try {
            // Trenutni broj klikova danas (po day)
            $todayCount = Click::query()
                ->where('from_company_id', $company->id)
                ->whereDate('day', now()->toDateString())
                ->count();

            if ($todayCount >= $limit) {
                return response()->json([
                    'success'     => false,
                    'message'     => __('Dnevni limit je dosegnut.'),
                    'todayClicks' => $todayCount,
                ], 409);
            }

            $targetCompanyId = (int) $request->input('target_company_id');

            // Sljedeći slot (bez transakcije; ako treba savršena atomarnost, stavi unique indeks na (from_company_id, day, slot))
            $maxSlot = (int) Click::query()
                ->where('from_company_id', $company->id)
                ->whereDate('day', now()->toDateString())
                ->max('slot');

            $nextSlot = $maxSlot + 1; // 1-based

            // Osvježi dnevnu sesiju
            $session = DailySession::firstOrCreate(
                ['company_id' => $company->id, 'day' => now()->toDateString()],
                ['slots_payload' => json_encode([])]
            );

            $payload = json_decode($session->slots_payload, true) ?? [];
            if (!in_array($nextSlot, $payload, true)) {
                $payload[] = $nextSlot;
            }
            $session->slots_payload   = json_encode($payload);
            $session->completed_count = count($payload);

            // Ako je dosegnut limit, opcionalna aktivacija linka uz uvjet referralsa
            if ($session->completed_count >= $limit) {
                $referralCount = ReferralLink::query()->where('user_id', $user->id)->count();
                if ($referralCount >= $ref_limit) {
                    $company->update(['is_link_active' => true]);
                }
                $session->completed_25 = true;
            }

            $session->save();

            // Upis klika
            Click::create([
                'company_id'      => $targetCompanyId,
                'from_company_id' => $company->id,
                'day'             => now()->toDateString(),
                'slot'            => $nextSlot,
                'link_url'        => (string) $request->input('url', ''),
                'ip'              => $request->ip(),
                'user_agent'      => $request->userAgent(),
                'user_id'         => $user->id,
            ]);

            // Statistika kompanije
            $company->increment('clicks');

            // Novo stanje
            $todayClicks = Click::query()
                ->where('from_company_id', $company->id)
                ->whereDate('day', now()->toDateString())
                ->count();

            $visitedTodayCompanyIds = Click::query()
                ->where('from_company_id', $company->id)
                ->whereDate('day', now()->toDateString())
                ->pluck('company_id')
                ->map(fn ($v) => (int) $v)
                ->all();

            return response()->json([
                'success'           => true,
                'todayClicks'       => $todayClicks,
                'nextSlot'          => $nextSlot,
                'visitedCompanyIds' => $visitedTodayCompanyIds,
            ]);
        } catch (\Throwable $e) {
            Log::error('Click failed', [
                'message'   => $e->getMessage(),
                'companyId' => $company->id ?? null,
                'userId'    => $user->id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Došlo je do greške. Pokušajte ponovno.'),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'url'   => ['required', 'email', 'max:255'], // sada email
            'label' => ['nullable', 'string', 'max:120'],
        ]);

        $user    = auth()->user();
        $company = $user->company; // može biti null
        $userId  = $user->id;

        if (!$company) {
            return redirect()->route('front.account.dashboard');
        }

        $countToday = ReferralLink::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        $ref_limit = (int) app_settings()->referralsRequired();

        if ($countToday >= $ref_limit) {
            return back()->with('status', __('Dnevni limit linkova je dosegnut.'));
        }

        $token  = Str::uuid()->toString();
        $refUrl = route('register', ['ref' => $token]); // npr. /register?ref=TOKEN

        $link = ReferralLink::create([
            'user_id' => $userId,
            'url'     => $refUrl,
            'label'   => $request->string('label') ?: $request->string('url'),
        ]);

        $company->increment('referrals_count');

        // Ako želiš ovdje aktivirati link pri dosegu referralsa, odkomentiraj:
        // if ($link && ($countToday + 1) == $ref_limit) {
        //     $company->update(['is_link_active' => true]);
        // }

        Mail::to($request->input('url'))->send(
            new ReferralInvitationMail($user, $company, $refUrl)
        );

        return back()->with('status', __('Pozivnica je poslana.'));
    }
}
