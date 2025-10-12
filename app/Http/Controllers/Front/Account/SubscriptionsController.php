<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\Controller;
use App\Services\Billing\InvoiceService;
use App\Services\Settings\SettingsManager;

class SubscriptionsController extends Controller
{

    public function index(SettingsManager $settings)
    {
        $user = auth()->user();
        $invoices = (new InvoiceService())->forUser($user);
        $statuses = collect($settings->get('order_statuses', 'list', []));
        $payments = collect($settings->paymentsActive());

        //dd($payments, $statuses, $statuses->where('id', 3)->first());

        return view('front.account.payments', compact('user', 'payments', 'invoices', 'statuses'));
    }


    public function invoices(InvoiceService $invoices)
    {
        $userInvoices = $invoices->forUser(auth()->user());

        return view('front.account.invoices', compact('userInvoices'));
    }


    public function subscriptions()
    {
        $company = auth()->user()->company()->first();

        if (! $company) {
            return view('front.account.subscriptions', [
                'subs' => collect(),
            ]);
        }

        $subs = $company->subscriptions()->latest()->get();

        return view('front.account.subscriptions', compact('subs'));
    }


    public function downloadInvoice($invoiceId)
    {
        $invoice = auth()->user()->invoices()->findOrFail($invoiceId);

        return $invoice->downloadPdf(); // ili Storage::download($invoice->path)
    }
}
