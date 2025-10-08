<?php

namespace App\Payments\Providers\Corvus;

use App\Models\Back\Billing\Payment;
use App\Models\Back\Billing\Subscription;
use App\Models\Back\Catalog\Company;
use App\Payments\Contracts\PaymentProviderInterface;
use App\Services\Settings\SettingsManager;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Driver implements PaymentProviderInterface
{
    protected const TEST_URL = 'https://test-wallet.corvuspay.com/checkout/';
    protected const LIVE_URL = 'https://wallet.corvuspay.com/checkout/';

    public static function code(): string
    {
        return 'corvus';
    }


    // returns array; controller may cast to (object) when persisting (same as currencies)
    public static function defaultTitle(): array
    {
        return ['hr' => 'Corvus', 'en' => 'Corvus'];
    }


    public static function defaultConfig(): array
    {
        return [
            'price'             => null,
            'short_description' => ['hr' => null, 'en' => null],
            'description'       => ['hr' => null, 'en' => null],
            'shop_id'           => '',
            'secret_key'        => '',
            'callback'          => route('order'),
            'test'              => 1, // 1|0
            'currency'          => 'EUR',

            // statični (predefinirani) endpointi — mogu se pregaziti iz DB-a
            'gateway'           => [
                'live' => 'https://form.wspay.biz/Authorization.aspx',
                'test' => 'https://formtest.wspay.biz/Authorization.aspx',
            ],
        ];
    }


    public static function validateResponse(Request $request, Company $company): array
    {
        if ( ! $request->has('approval_code')) {
            abort(404);
        }

        $selectedPlan = (new SettingsManager())->paymentByCode(self::code());
        $provjera     = explode('-', $request->input('approval_code'));
        $subscription = Subscription::query()
                                    ->where('id', $provjera[0])
                                    ->where('company_id', $company->id)
                                    ->where('status', 'active')
                                    ->first();

        if ( ! $subscription || ! $selectedPlan) {
            return [
                'message'  => 'error',
                'response' => 'Nema aktivne pretplate za ovu kompaniju.'
            ];
        }

        $period    = $subscription->period;
        $start     = Carbon::today();
        $periodEnd = $period === 'monthly'
            ? (clone $start)->addMonth()->subDay()
            : (clone $start)->addYear()->subDay(); // default yearly

        $netPrice = (float) ($selectedPlan['price'] ?? 0); // neto (bez PDV-a)
        // Neto → tax
        $net = round($netPrice, 2);
        $tax = round($subscription->price - $net, 2);
        $invoiceNo = 'INV-' . $company->id . '-' . $subscription->id . '-' . date('Y');

        $payment_data = [
            'company_id'      => $company->id,
            'subscription_id' => $subscription->id,
            'amount'          => $subscription->price,
            'vat_rate'        => vat_rate(),
            'tax_amount'      => $tax,
            'net_amount'      => $net,
            'currency'        => $subscription->currency,
            'status'          => config('settings.payments.new_status'),
            'period_start'    => $start->toDateString(),
            'period_end'      => $periodEnd->toDateString(),
            'issued_at'       => $start->toDateString(),
            'paid_at'         => null,
            'invoice_no'      => $invoiceNo,
            'provider'        => $selectedPlan['name'],
            'method'          => $selectedPlan['code'],
            'provider_ref'    => $selectedPlan['driver'],
            'meta'            => null,
        ];

        $payment = Payment::query()->where('company_id', $company->id)
                          ->where('subscription_id', $subscription->id)
                          ->where('invoice_no', $invoiceNo)
                          ->first();

        if ($payment) {
            unset($payment_data['invoice_no']);

            $payment->update($payment_data);

        } else {
            $payment = Payment::query()->create($payment_data);
        }

        return [
            'message'      => 'success',
            'subscription' => $subscription,
            'payment'      => $payment
        ];
    }


    /** Controller će pozvati ovo da pripremi $data za blade */
    public static function buildFrontData(array $data): array
    {
        $settings = new SettingsManager();
        $plan = $settings->paymentByCode(self::code());
        $company = Company::find($data['company_id']);
        
        if ( ! $plan || ! $company) {
            abort(404);
        }
        
        if ($plan['config']['test'] === 1) {
            $url = self::TEST_URL;
        } else {
            $url = self::LIVE_URL;
        }

        $total = number_format($data['price'], 2, '.', '');

        $response['currency']  = $data['currency'] ?? $plan['config']['currency'];
        $response['action']    = $url;
        $response['merchant']  = $plan['config']['shop_id'];
        $response['order_id']  = $data['id'] . '-' . now()->format('Y');
        $response['total']     = $total;
        $response['firstname'] = $company->t_name;
        $response['lastname']  = '';
        $response['address']   = $company->street . ' ' . $company->street_no;
        $response['city']      = $company->city;
        $response['country']   = $company->state;
        $response['postcode']  = '10000';
        $response['telephone'] = $company->phone;
        $response['email']     = $company->email;
        $response['lang']      = 'hr';
        $response['plan']      = '01';
        $response['cc_name']   = 'VISA';//...??
        $response['rate']      = 1;
        $response['return']    = $data['config']['callback'];
        $response['cancel']    = route('index');
        $response['method']    = 'GET';

        $response['number_of_installments'] = 'Y0299';

        $string = 'amount' . $total .
                  'cardholder_email' . $response['email'] .
                  'cardholder_name' . $response['firstname'] .
                  'cardholder_phone' . $response['telephone'] .
                  'cardholder_surname' . $response['lastname'] .
                  'cartWeb shop kupnja ' . $response['order_id'] .
                  'currency' . $response['currency'] .
                  'language' . $response['lang'] .
                  'order_number' . $response['order_id'] .
                  'payment_all' . $response['number_of_installments'] .
                  'require_completefalsestore_id' . $response['merchant'] .
                  'version1.3';

        $keym = $data['config']['secret_key'];
        $hash = hash_hmac('sha256', $string, $keym);

        $response['md5'] = $hash;

        return $response;
    }


    public static function frontBlade(): string
    {
        // not used now; kept to satisfy interface
        return 'front.checkout.payments.wspay';
    }


    protected static function formatTotal($amount): array
    {
        $n       = is_numeric($amount)
            ? (float) $amount
            : (float) str_replace(',', '.', preg_replace('/[^\d,\.]/', '', (string) $amount));
        $display = number_format($n, 2, ',', '');   // "1234,56"
        $forHash = str_replace(',', '', $display);  // "1234.56"

        return [$display, $forHash];
    }
}
