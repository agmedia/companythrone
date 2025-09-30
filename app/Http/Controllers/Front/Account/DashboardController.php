<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\Controller;
use App\Models\Shared\ReferralLink;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $company = $user->company; // jer si povezao company sa user_id

        $limitPerDay = 25;

        // koliko je klikova odrađeno danas
        $todayClicks = \App\Models\Shared\Click::query()
                                               ->where('from_company_id', $company->id)
                                               ->whereDate('created_at', now()->toDateString())
                                               ->count();

        // koliko referral linkova imam (za uvjet aktivacije)
        $refCount = \App\Models\Shared\ReferralLink::query()
                                                   ->where('user_id', $user->id)
                                                   ->count();

        return view('front.account.dashboard', [
            'user'        => $user,
            'todayClicks' => $todayClicks,
            'limitPerDay' => $limitPerDay,
            'refCount'    => $refCount,
        ]);
    }

}
