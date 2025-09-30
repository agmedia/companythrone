<?php

namespace App\Services\Billing;

use App\Models\User;
use Illuminate\Support\Collection;

class InvoiceService
{

    public function forUser(User $user): Collection
    {
        // Ako user ima 1 company (kao owner)
        $company = $user->company()->first();

        if ( ! $company) {
            return collect();
        }

        return $company->payments()
                       ->with('subscription')
                       ->latest('issued_at')
                       ->get()
                       ->map(function ($payment) {
                           return [
                               'number'   => $payment->invoice_no ?? 'INV-' . $payment->id,
                               'date'     => $payment->issued_at ?? $payment->created_at,
                               'amount'   => $payment->amount,
                               'currency' => $payment->currency,
                               'status'   => $payment->status,
                               'provider' => $payment->provider,
                               'method'   => $payment->method,
                               'period'   => $payment->period_start && $payment->period_end
                                   ? $payment->period_start->format('d.m.Y') . ' – ' . $payment->period_end->format('d.m.Y')
                                   : null,
                           ];
                       });
    }
}
