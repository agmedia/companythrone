<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Back\Billing\Payment;
use App\Models\Back\Settings\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // ✅ dohvat statusa iz settings
        $statuses = collect(json_decode(
            Settings::where('code', 'order_statuses')->where('key', 'list')->value('value'),
            true
        ));

        // ✅ filter parametri
        $filters = [
            'status' => $request->get('status'),
            'company' => $request->get('company'),
            'provider' => $request->get('provider'),
        ];

        $query = Payment::query()->with(['company', 'subscription']);

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['company']) {
            $query->whereHas('company', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['company'] . '%');
            });
        }

        if ($filters['provider']) {
            $query->where('provider', 'like', '%' . $filters['provider'] . '%');
        }

        $payments = $query->latest()->paginate(25)->withQueryString();

        return view('admin.payments.index', [
            'payments' => $payments,
            'statuses' => $statuses,
            'filters'  => $filters,
        ]);
    }

    public function edit(Payment $payment)
    {
        $statuses = collect(json_decode(
            Settings::where('code', 'order_statuses')->where('key', 'list')->value('value'),
            true
        ));

        return view('admin.payments.edit', [
            'payment'  => $payment,
            'statuses' => $statuses,
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $statuses = collect(json_decode(
            Settings::where('code', 'order_statuses')->where('key', 'list')->value('value'),
            true
        ));

        $validIds = $statuses->pluck('id')->map(fn($v) => (string)$v)->toArray();

        $data = $request->validate([
            'status'      => ['required', Rule::in($validIds)],
            'invoice_no'  => ['nullable', 'string', 'max:255'],
            'provider'    => ['nullable', 'string', 'max:255'],
            'paid_at'     => ['nullable', 'date'],
        ]);

        /**
         * The HTTP request instance.
         *
         * This object represents an incoming HTTP request. It is utilized to retrieve
         * input data, query parameters, cookies, files, and other request-based data.
         * Extends from Symfony\Component\HttpFoundation\Request.
         *
         * @var \Illuminate\Http\Request
         */
        if ( ! $request->hasAny(['invoice_no', 'provider', 'paid_at']) && $request->has(['status'])) {
            $status = $statuses->firstWhere('id', $request->integer('status'));

            if (in_array($status['id'], config('settings.payments.paid_status'))) {
                $data['paid_at'] = now();
            }

            $payment->fill($data)->save();

            return response()->json([
                'success' => true,
                'label'   => $status['title'][app()->getLocale()] ?? ucfirst($payment->status),
                'color'   => $status['color'] ?? 'secondary',
            ]);
        }

        $payment->fill($data)->save();

        return redirect()
            ->route('payments.index')
            ->with('status', __('Uplata je ažurirana.'));
    }
}
