<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\Controller;
use App\Mail\ReferralInvitationMail;
use App\Models\Back\Catalog\Company;
use App\Models\Shared\Click;
use App\Models\Shared\DailySession;
use App\Models\Shared\ReferralLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LinksController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $company = auth()->user()->company; // može biti null

        $referrals = ReferralLink::query()
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $referralCount = $referrals->count();

        $links = ReferralLink::query()
            ->where('user_id', $userId)
            ->latest()
            ->paginate(20);

        $today = ReferralLink::query()
            ->where('user_id', $userId)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        // Session i targets samo ako postoji company
        if ($company) {
            $session = DailySession::firstOrCreate(
                ['company_id' => $company->id, 'day' => now()->toDateString()],
                ['slots_payload' => json_encode([])]
            );

            // Izvuci payload i dekodiraj u array
            $usedSlots = json_decode($session->slots_payload, true) ?? [];

// Dohvati kompanije koje nisu već odabrane
            $targets = Company::query()
                ->where('id', '!=', $company->id)
                ->where('is_published', 1)
                ->whereNotIn('id', $usedSlots) // filtriraj već kliknute
                ->inRandomOrder()
                ->limit(25 - count($usedSlots)) // koliko još nedostaje do 25
                ->get(['id', 'weburl']);

           /* $targets = Company::query()
                ->where('id', '!=', $company->id)
                ->inRandomOrder()
                ->limit(25)
                ->get(['id', 'weburl']); */
        } else {
            $session = null;
            // ako user nema company, samo daj random targete (bez isključenja vlastite)
            $targets = Company::query()
                ->inRandomOrder()
                ->limit(25)
                ->get(['id', 'weburl']);
        }

        return view('front.account.links', [
            'links'            => $links,
            'todayLinks'       => $today,
            'limitPerDay'      => 25,
            'session'          => $session,     // može biti null → u bladeu provjeri s @isset
            'targets'          => $targets,
            'referrals'        => $referrals,
            'referralCount'    => $referralCount,
            'referralRequired' => 5,
        ]);
    }

    public function click(Request $request)
    {
        $request->validate([
            'slot'              => 'required|integer|min:1|max:25',
            'target_company_id' => 'required|integer|exists:companies,id',
            'url'               => 'nullable|string|max:2048',
        ]);

        $user = auth()->user();
        $company = $user->company; // može biti null

        // Ako postoji company → vodi dnevnu sesiju i slotove; inače samo zabilježi klik
        if ($company) {
            $session = DailySession::firstOrCreate(
                ['company_id' => $company->id, 'day' => now()->toDateString()],
                ['slots_payload' => json_encode([])]
            );

            $payload = json_decode($session->slots_payload, true) ?? [];

            if (!in_array($request->slot, $payload, true)) {
                $payload[]                = $request->slot;
                $session->slots_payload   = json_encode($payload);
                $session->completed_count = count($payload);

                $referralCount = ReferralLink::query()->where('user_id', $user->id)->count();

                if ($session->completed_count >= 25) {
                    $session->completed_25 = true;

                    if ($referralCount >= 5) {
                        // zaštita ako model Company ima kolonu is_link_active
                        $company->update(['is_link_active' => true]);
                    }
                }
                $session->save();

                Click::create([
                    'company_id'      => $request->target_company_id,
                    'from_company_id' => $company->id, // imamo izvor
                    'day'             => now()->toDateString(),
                    'slot'            => $request->slot,
                    'link_url'        => $request->input('url', ''),
                    'ip'              => $request->ip(),
                    'user_agent'      => $request->userAgent(),
                    // Ako tvoja tablica `clicks` ima `user_id`, možeš dodati:
                    'user_id'         => $user->id,
                ]);
            }
        } else {
            // Nema company: preskoči session/slotove, samo evidentiraj klik bez from_company_id
            Click::create([
                'company_id' => $request->target_company_id,
                'day'        => now()->toDateString(),
                'slot'       => $request->slot,
                'link_url'   => $request->input('url', ''),
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id'    => $user->id, // preporuka: spremi tko je kliknuo
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'url'   => ['required', 'email', 'max:255'], // sada email
            'label' => ['nullable','string','max:120'],
        ]);

        $user = auth()->user();
        $userId = $user->id;

        // dnevni limit 25
        $countToday = ReferralLink::where('user_id', $userId)
                                  ->whereDate('created_at', today())
                                  ->count();

        abort_if($countToday >= 25, 429, __('Dnevni limit linkova je dosegnut.'));

        $token = Str::uuid()->toString();
        $refUrl = route('register', ['ref' => $token]); // npr. /register?ref=TOKEN

        $link = ReferralLink::create([
            'user_id' => $userId,
            'url'     => $refUrl,
            'label'   => $request->string('label') ?: $request->string('url'),
        ]);

        // pošalji poziv
        Mail::to($request->input('url'))->send(
            new ReferralInvitationMail($user, $refUrl)
        );

        // zapamti token u session (ako treba)
        return back()->with('status', __('Pozivnica je poslana.'));
    }

}
