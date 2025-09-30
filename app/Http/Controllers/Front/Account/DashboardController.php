<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\Controller;
use App\Models\Shared\ReferralLink;

class DashboardController extends Controller
{

    public function index()
    {
        $user       = auth()->user();
        $todayCount = ReferralLink::query()
                                  ->where('user_id', $user->id)
                                  ->whereDate('created_at', now()->toDateString())
                                  ->count();

        return view('front.account.dashboard', [
            'user'        => $user,
            'todayLinks'  => $todayCount,
            'limitPerDay' => 25,
        ]);
    }
}
