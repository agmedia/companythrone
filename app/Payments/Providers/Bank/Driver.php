<?php

namespace App\Payments\Providers\Bank;

use App\Models\Back\Billing\Payment;
use App\Models\Back\Billing\Subscription;
use App\Models\Back\Catalog\Company;
use App\Payments\Contracts\PaymentProviderInterface;
use App\Services\Settings\SettingsManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Driver implements PaymentProviderInterface
{

    protected const BARCODE_URL = 'https://hub3.bigfish.software/api/v2/barcode';


    public static function code(): string
    {
        return 'bank';
    }


    public static function defaultTitle(): array
    {
        return ['hr' => 'Općom uplatnicom / Virmanom / Internet bankarstvom / qr kod', 'en' => 'Bank Transfer'];
    }


    public static function defaultConfig(): array
    {
        return [
            'account_name' => '',
            'iban'         => '',
            'swift'        => '',
            'bank_name'    => '',
            'instructions' => [
                'hr' => 'Molimo uplatite na navedeni IBAN.',
                'en' => 'Please transfer to the specified IBAN.',
            ],
        ];
    }


    public static function validateResponse(Request $request, Company $company): array
    {
        if ( ! $request->has('provjera')) {
            abort(404);
        }

        $selectedPlan = (new SettingsManager())->paymentByCode(self::code());
        $provjera     = explode('-', $request->input('provjera'));
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
            'provider'        => $selectedPlan['name']->{current_locale()},
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
            'payment'      => $payment,
            'qr_code'      => '/media/img/qr/' . $subscription->id . '.jpg'
        ];
    }


    /** Controller će pozvati ovo da pripremi $data za blade */
    public static function buildFrontData(array $data): array
    {
        self::create_QR($data[0]);

        return [
            'id'       => $data[0]['id'] . '-' . date('Y'),
            'callback' => route('order'),
        ];
    }


    public static function frontBlade(): string
    {
        return 'front.checkout.payments.bank';
    }

    /*******************************************************************************
    *                                Copyright : AGmedia                           *
    *                              email: filip@agmedia.hr                         *
    *******************************************************************************/

    private static function create_QR(array $subscription): array
    {
        $pozivnabroj = $subscription['id'] . '-' . date("ym");
        $total       = str_replace(',', '', number_format($subscription['price'], 2, ',', ''));

        $hubstring = array(
            'renderer' => 'image',
            'options'  =>
                array(
                    "format"  => "jpg",
                    "padding" => 20,
                    "color"   => "#2c3e50",
                    "bgColor" => "#fff",
                    "scale"   => 3,
                    "ratio"   => 3
                ),
            'data'     =>
                array(
                    'amount'      => (int) $total,
                    'currency'    => 'EUR',
                    'sender'      =>
                        array(
                            'name'   => $subscription['company']['name'],
                            'street' => $subscription['company']['street'] . ' ' . $subscription['company']['street_no'],
                            'place'  => $subscription['company']['city'],
                        ),
                    'receiver'    =>
                        array(
                            'name'      => 'CompanyThrone',
                            'street'    => 'Put Gvozdenova 283',
                            'place'     => '10000 Zagreb',
                            'iban'      => 'HR7023900011100000000',
                            'model'     => '00',
                            'reference' => $pozivnabroj,
                        ),
                    'purpose'     => 'CMDT',
                    'description' => 'Pretplata CompanyThrone',
                ),
        );

        $ch = curl_init(self::BARCODE_URL);

        # Setting our options
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($hubstring));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        # Get the response

        $response = curl_exec($ch);
        curl_close($ch);

        $response = base64_encode($response);

        $scimg = 'data:image/png;base64,' . $response;
        list($type, $scimg) = explode(';', $scimg);
        list(, $scimg) = explode(',', $scimg);
        $scimg = base64_decode($scimg);

        $path = $subscription['id'] . '.jpg';

        Storage::disk('qr')->put($path, $scimg);

        return ['uplatnica' => $response];
    }
}
