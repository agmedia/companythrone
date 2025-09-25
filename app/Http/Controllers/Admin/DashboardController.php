<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Back\Billing\Payment;
use App\Models\Back\Billing\Subscription;
use App\Models\Back\Catalog\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{

    public function index()
    {
        $now = now();

        // --- Top cards ---
        $companiesTotal   = Company::count();
        $companiesActive  = Company::where('is_published', true)->count();
        $subsActive       = Subscription::whereIn('status', ['active','trialing'])->count();
        $paymentsPending  = Payment::where('status', 'pending')->count();
        $upcomingRenewals = Subscription::whereDate('next_renewal_on', '>=', $now->toDateString())
                                        ->whereDate('next_renewal_on', '<=', $now->copy()->addDays(7)->toDateString())
                                        ->count();

        // MRR (yearly / 12 + monthly)
        $mrr = Subscription::whereIn('status', ['active','trialing'])
                           ->get()
                           ->sum(fn ($s) => $s->period === 'yearly' ? ($s->price / 12) : $s->price);

        // Revenue this month (paid)
        $revenueThisMonth = Payment::where('status', 'paid')
                                   ->whereMonth('paid_at', $now->month)
                                   ->whereYear('paid_at', $now->year)
                                   ->sum('amount');

        // --- Charts data ---

        // 14 dana: broj i suma uplata po danu (koristimo COALESCE za datume)
        $from = $now->copy()->subDays(13)->startOfDay();
        $raw = Payment::selectRaw("
                DATE(COALESCE(paid_at, issued_at, created_at)) as d,
                COUNT(*) as c,
                SUM(CASE WHEN status='paid' THEN amount ELSE 0 END) as s
            ")
                      ->whereDate(DB::raw('COALESCE(paid_at, issued_at, created_at)'), '>=', $from->toDateString())
                      ->groupBy('d')->orderBy('d')->get()
                      ->keyBy('d');

        $labels = [];
        $paymentsCount = [];
        $paymentsSum = [];
        for ($i = 0; $i < 14; $i++) {
            $day = $from->copy()->addDays($i)->toDateString();
            $labels[] = $day;
            $paymentsCount[] = (int) ($raw[$day]->c ?? 0);
            $paymentsSum[]   = (float) ($raw[$day]->s ?? 0.0);
        }

        // Subscription status breakdown (donut)
        $statusCounts = Subscription::select('status', DB::raw('COUNT(*) as c'))
                                    ->groupBy('status')->pluck('c', 'status')->all();
        $subStatusLabels = array_values(array_map('ucfirst', array_keys($statusCounts)));
        $subStatusData   = array_values($statusCounts);

        // Latest payments table
        $latestPayments = Payment::with('company:id,email')
                                 ->latest('id')->limit(10)->get();

        return view('admin.dashboard.index', compact(
            'companiesTotal','companiesActive','subsActive','paymentsPending','upcomingRenewals',
            'mrr','revenueThisMonth',
            'labels','paymentsCount','paymentsSum',
            'subStatusLabels','subStatusData',
            'latestPayments'
        ));
    }

    public function maintenanceOn(): RedirectResponse
    {
        // Tajni "bypass" ključ da se ne zaključaš van
        $secret = 'agm';

        // Laravel maintenance ON (sa bypass URL-om /{secret})
        Artisan::call('down', [
            '--secret' => $secret,
            '--retry'  => 60, // Retry-After header (sekunde)
        ]);

        Cache::put('maintenance:secret', $secret, now()->addHours(6));

        return back()->with('success', 'Maintenance ON. Bypass URL: ' . url($secret));
    }

    public function maintenanceOff(): RedirectResponse
    {
        Artisan::call('up');
        Cache::forget('maintenance:secret');

        return back()->with('success', 'Maintenance OFF.');
    }

    public function clearCache(): RedirectResponse
    {
        // Briše sve bitne cacheve odjednom
        Artisan::call('optimize:clear');

        return back()->with('success', 'Cache, config, route i view cache su očišćeni.');
    }
}
