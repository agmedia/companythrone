<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Company;
use App\Models\Shared\Click;
use App\Models\Shared\DailySession;
use App\Models\Shared\ReferralLink;
use Illuminate\Http\Request;

class LinksController extends Controller
{

    public function index()
    {
        $referrals = ReferralLink::query()
                                 ->where('user_id', auth()->id())
                                 ->latest()
                                 ->get();

        $referralCount = $referrals->count();

        $links = ReferralLink::query()
                             ->where('user_id', auth()->id())
                             ->latest()
                             ->paginate(20);

        $today = ReferralLink::query()
                             ->where('user_id', auth()->id())
                             ->whereDate('created_at', now()->toDateString())
                             ->count();

        $company = auth()->user()->company;
        $session = DailySession::firstOrCreate(
            ['company_id' => $company->id, 'day' => now()->toDateString()],
            ['slots_payload' => json_encode([])]
        );

        // Izaberi npr. 25 random tvrtki za gumbe
        $targets = Company::query()
                          ->where('id', '!=', $company->id)
                          ->inRandomOrder()
                          ->limit(25)
                          ->get(['id', 'weburl']);

        return view('front.account.links', [
            'links'       => $links,
            'todayLinks'  => $today,
            'limitPerDay' => 25,
            'session'     => $session,
            'targets'     => $targets,
            'referrals' => $referrals,
            'referralCount' => $referralCount,
            'referralRequired' => 5,
        ]);
    }


    public function click(Request $request)
    {
        $request->validate([
            'slot'              => 'required|integer|min:1|max:25',
            'target_company_id' => 'required|integer|exists:companies,id',
        ]);

        $company = auth()->user()->company;
        $session = DailySession::firstOrCreate(
            ['company_id' => $company->id, 'day' => now()->toDateString()],
            ['slots_payload' => json_encode([])]
        );

        $payload = json_decode($session->slots_payload, true) ?? [];
        if ( ! in_array($request->slot, $payload)) {
            $payload[]                = $request->slot;
            $session->slots_payload   = json_encode($payload);
            $session->completed_count = count($payload);

            $referralCount = ReferralLink::query()->where('user_id', auth()->id())->count();

            if ($session->completed_count >= 25) {
                $session->completed_25 = true;

                if ($referralCount >= 5) {
                    $company->update(['is_link_active' => true]);
                }
            }
            $session->save();

            Click::create([
                'company_id'      => $request->target_company_id,
                'from_company_id' => $company->id,
                'day'             => now()->toDateString(),
                'slot'            => $request->slot,
                'link_url'        => $request->input('url', ''), // ako želiš
                'ip'              => $request->ip(),
                'user_agent'      => $request->userAgent(),
            ]);
        }

        return response()->json(['success' => true]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            // po želji: 'label' => ['nullable','string','max:120'],
        ]);

        // Dnevni limit 25
        $countToday = ReferralLink::query()
                                  ->where('user_id', auth()->id())
                                  ->whereDate('created_at', now()->toDateString())
                                  ->count();

        abort_if($countToday >= 25, 429, __('Dnevni limit linkova je dosegnut.'));

        ReferralLink::create([
            'user_id' => auth()->id(),
            'url'     => $request->string('url'),
            'label'   => $request->string('label') ?: null,
        ]);

        return back()->with('status', __('Link dodan.'));
    }
}
