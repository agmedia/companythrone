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
use Illuminate\Support\Facades\DB;

class LinksController extends Controller
{
    public function index()
    {
        $user      = auth()->user();
        $userId    = $user->id;
        $company   = $user->company; // može biti null

        if (!$company) {
            return redirect()->route('front.account.dashboard');
        }

        $referrals     = ReferralLink::query()->where('user_id', $userId)->latest()->get();
        $referralCount = $referrals->count();

        $links  = ReferralLink::query()->where('user_id', $userId)->latest()->paginate(10);
        $today  = ReferralLink::query()->where('user_id', $userId)->whereDate('created_at', now()->toDateString())->count();

        // postavke
        $sm         = new SettingsManager();
        $limit      = (int) $sm->get('company', 'auth_clicks_required');
        $ref_limit  = (int) $sm->get('company', 'auth_referrals_required');

        // osiguraj dnevnu sesiju
        $session = DailySession::firstOrCreate(
            ['company_id' => $company->id, 'day' => now()->toDateString()],
            ['slots_payload' => json_encode([])]
        );

        // već posjećene kompanije DANAS (lista company_id)
        $usedCompanyIds = Click::query()
            ->where('from_company_id', $company->id)
            ->whereDate('day', now()->toDateString())
            ->pluck('company_id');

        // koliko je danas klikova (po danu)
        $todayClicks = Click::query()
            ->where('from_company_id', $company->id)
            ->whereDate('day', now()->toDateString())
            ->count();

        // preostalo do limita (spriječi negativan limit())
        $remaining = max(0, $limit - $usedCompanyIds->count());

        // mete (isključi vlastitu, uzmi samo aktivne i ne-kliknute danas)
        $targets = Company::query()
            ->where('id', '!=', $company->id)
            ->where('is_published', 1)
            ->where('is_link_active', 1)
            ->whereNotIn('id', $usedCompanyIds)
            // ->inRandomOrder()
            ->limit($remaining)
            ->get(['id', 'weburl', 't_name']);

        return view('front.account.links', [
            'links'            => $links,
            'todayLinks'       => $today,
            'limitPerDay'      => $limit,
            'todayClicks'      => $todayClicks,
            'session'          => $session,         // može biti null → u bladeu provjeri s @isset
            'usedSlots'        => $usedCompanyIds,  // zapravo: visited company IDs danas
            'targets'          => $targets,
            'referrals'        => $referrals,
            'referralCount'    => $referralCount,
            'referralRequired' => $ref_limit,
        ]);
    }

    public function click(Request $request)
    {
        $request->validate([
            // slot više ne primamo s fronta; server ga računa
            'target_company_id' => 'required|integer|exists:companies,id',
            'url'               => 'nullable|string|max:2048',
        ]);

        $user    = auth()->user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('front.account.dashboard');
        }

        $limit     = (int) app_settings()->clicksRequired();
        $ref_limit = (int) app_settings()->referralsRequired();

        try {
            $result = DB::transaction(function () use ($request, $user, $company, $limit, $ref_limit) {
                // lock session (sprječava race)
                $session = DailySession::lockForUpdate()->firstOrCreate(
                    ['company_id' => $company->id, 'day' => now()->toDateString()],
                    ['slots_payload' => json_encode([])]
                );

                // trenutno stanje danas (lock)
                $todayCount = Click::query()
                    ->where('from_company_id', $company->id)
                    ->whereDate('day', now()->toDateString())
                    ->lockForUpdate()
                    ->count();

                if ($todayCount >= $limit) {
                    return [
                        'success'     => false,
                        'status_code' => 409,
                        'todayClicks' => $todayCount,
                        'message'     => __('Dnevni limit je dosegnut.'),
                    ];
                }

                $targetCompanyId = (int) $request->input('target_company_id');

                // odredi sljedeći slot server-side
                $maxSlot = (int) Click::query()
                    ->where('from_company_id', $company->id)
                    ->whereDate('day', now()->toDateString())
                    ->max('slot');

                $nextSlot = $maxSlot + 1; // 1-based

                // osvježi session payload / brojač
                $payload = json_decode($session->slots_payload, true) ?? [];
                if (!in_array($nextSlot, $payload, true)) {
                    $payload[] = $nextSlot;
                }
                $session->slots_payload   = json_encode($payload);
                $session->completed_count = count($payload);

                // aktivacija linka ako su ispunjeni uvjeti
                if ($session->completed_count >= $limit) {
                    $referralCount = ReferralLink::query()->where('user_id', $user->id)->count();
                    if ($referralCount >= $ref_limit) {
                        $company->update(['is_link_active' => true]);
                    }
                    $session->completed_25 = true;
                }

                $session->save();

                // upiši klik
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

                $company->increment('clicks');

                // ponovno stanje
                $todayClicks = Click::query()
                    ->where('from_company_id', $company->id)
                    ->whereDate('day', now()->toDateString())
                    ->count();

                $visitedTodayCompanyIds = Click::query()
                    ->where('from_company_id', $company->id)
                    ->whereDate('day', now()->toDateString())
                    ->pluck('company_id')
                    ->map(fn($v) => (int) $v)
                    ->all();

                return [
                    'success'             => true,
                    'status_code'         => 200,
                    'todayClicks'         => $todayClicks,
                    'nextSlot'            => $nextSlot,
                    'visitedCompanyIds'   => $visitedTodayCompanyIds,
                ];
            });

            if (!$result['success']) {
                return response()->json([
                    'success'     => false,
                    'message'     => $result['message'] ?? __('Greška.'),
                    'todayClicks' => $result['todayClicks'] ?? null,
                ], $result['status_code'] ?? 400);
            }

            return response()->json([
                'success'           => true,
                'todayClicks'       => $result['todayClicks'],
                'nextSlot'          => $result['nextSlot'],
                'visitedCompanyIds' => $result['visitedCompanyIds'],
            ]);

        } catch (\Throwable $e) {
            Log::error('Click failed', ['err' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
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

        // ako želiš aktivirati link pri dosegu referralsa, odkomentiraj:
        // if ($link && ($countToday + 1) == $ref_limit) {
        //     $company->update(['is_link_active' => true]);
        // }

        Mail::to($request->input('url'))->send(
            new ReferralInvitationMail($user, $company, $refUrl)
        );

        return back()->with('status', __('Pozivnica je poslana.'));
    }
}
