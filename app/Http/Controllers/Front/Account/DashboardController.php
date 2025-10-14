<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\Controller;
use App\Models\Shared\Click;
use App\Models\Shared\ReferralLink;
use App\Services\Settings\SettingsManager;

class DashboardController extends Controller
{

    public function index()
    {
        $user        = auth()->user();
        $company     = $user->company; // jer si povezao company sa user_id
        $todayClicks = 0;

        $sm          = new SettingsManager();
        $limitPerDay = $sm->get('company', 'auth_clicks_required');
        $ref_limit   = $sm->get('company', 'auth_referrals_required');

        if ($company) {
            // koliko je klikova odrađeno danas
            $todayClicks = Click::query()
                                ->where('from_company_id', $company->id)
                                ->whereDate('created_at', now()->toDateString())
                                ->count();
        }

        // koliko referral linkova imam (za uvjet aktivacije)
        $refCount = ReferralLink::query()
                                ->where('user_id', $user->id)
                                ->count();

        return view('front.account.dashboard', [
            'user'        => $user,
            'todayClicks' => $todayClicks,
            'limitPerDay' => $limitPerDay,
            'refCount'    => $refCount,
            'ref_limit'   => $ref_limit,
        ]);
    }

}
