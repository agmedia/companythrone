<?php

// app/Http/Controllers/Admin/SubscriptionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\Subscription\UpdateSubscriptionRequest;
use App\Models\Back\Billing\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $q = Subscription::query()->with(['company:id,email']);

        // Filters
        if ($request->filled('status') && $request->status !== 'all') {
            $q->where('status', $request->status);
        }
        if ($request->filled('period') && in_array($request->period, ['monthly','yearly'], true)) {
            $q->where('period', $request->period);
        }
        if ($request->filled('auto') && in_array($request->auto, ['1','0'], true)) {
            $q->where('is_auto_renew', $request->auto === '1');
        }
        if ($request->filled('plan')) {
            $q->where('plan', 'like', '%'.$request->string('plan')->toString().'%');
        }
        if ($request->filled('email')) {
            $term = $request->string('email')->toString();
            $q->whereHas('company', fn($c) => $c->where('email', 'like', "%{$term}%"));
        }
        if ($request->filled('renew_from')) {
            $q->whereDate('next_renewal_on', '>=', $request->date('renew_from'));
        }
        if ($request->filled('renew_to')) {
            $q->whereDate('next_renewal_on', '<=', $request->date('renew_to'));
        }

        $subscriptions = $q->orderByDesc('id')->paginate(20)->withQueryString();

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['company','payments' => fn($q) => $q->latest()]);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function edit(Subscription $subscription)
    {
        $subscription->load('company');
        return view('admin.subscriptions.edit', compact('subscription'));
    }

    public function update(UpdateSubscriptionRequest $request, Subscription $subscription)
    {
        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription updated.');
    }

    // ---------- Quick actions ----------

    public function activate(Subscription $subscription)
    {
        if (!in_array($subscription->status, ['trialing','paused','canceled','expired'], true)) {
            return back()->with('error', 'Cannot activate from current status.');
        }

        $subscription->status = 'active';
        $subscription->is_auto_renew = true;
        if (empty($subscription->next_renewal_on)) {
            $subscription->next_renewal_on = $this->nextRenewalFrom(Carbon::today(), $subscription->period);
        }
        $subscription->canceled_at = null;
        $subscription->save();

        return back()->with('success', 'Subscription activated.');
    }

    public function pause(Subscription $subscription)
    {
        if ($subscription->status !== 'active') {
            return back()->with('error', 'Only active subscriptions can be paused.');
        }

        $subscription->status = 'paused';
        $subscription->is_auto_renew = false;
        $subscription->save();

        return back()->with('success', 'Subscription paused.');
    }

    public function resume(Subscription $subscription)
    {
        if ($subscription->status !== 'paused') {
            return back()->with('error', 'Only paused subscriptions can be resumed.');
        }

        $subscription->status = 'active';
        $subscription->is_auto_renew = true;
        if (empty($subscription->next_renewal_on)) {
            $subscription->next_renewal_on = $this->nextRenewalFrom(Carbon::today(), $subscription->period);
        }
        $subscription->save();

        return back()->with('success', 'Subscription resumed.');
    }

    public function cancel(Subscription $subscription)
    {
        if (!in_array($subscription->status, ['trialing','active','paused'], true)) {
            return back()->with('error', 'Cannot cancel from current status.');
        }

        $subscription->status = 'canceled';
        $subscription->is_auto_renew = false;
        $subscription->canceled_at = now();
        // ako nema definiran kraj, postavi na danas (ili kraj tekućeg perioda po tvom poslovnom pravilu)
        if (empty($subscription->ends_on)) {
            $subscription->ends_on = Carbon::today();
        }
        $subscription->save();

        return back()->with('success', 'Subscription canceled.');
    }

    private function nextRenewalFrom(Carbon $from, string $period): Carbon
    {
        return $period === 'yearly'
            ? $from->copy()->addYear()->startOfDay()
            : $from->copy()->addMonth()->startOfDay();
    }
}
