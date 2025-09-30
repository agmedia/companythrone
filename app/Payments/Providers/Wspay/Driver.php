<?php

namespace App\Payments\Providers\Wspay;

use App\Payments\Contracts\PaymentProviderInterface;

class Driver implements PaymentProviderInterface
{

    public static function code(): string
    {
        return 'wspay';
    }


    // returns array; controller may cast to (object) when persisting (same as currencies)
    public static function defaultTitle(): array
    {
        return ['hr' => 'WSPay', 'en' => 'WSPay'];
    }


    public static function defaultConfig(): array
    {
        return [
            'price'             => null,
            'short_description' => ['hr' => null, 'en' => null],
            'description'       => ['hr' => null, 'en' => null],
            'shop_id'           => '',
            'secret_key'        => '',
            'callback'          => url('/'),
            'test'              => 1, // 1|0
            'currency'          => 'EUR',

            // statični (predefinirani) endpointi — mogu se pregaziti iz DB-a
            'gateway'           => [
                'live' => 'https://form.wspay.biz/Authorization.aspx',
                'test' => 'https://formtest.wspay.biz/Authorization.aspx',
            ],
        ];
    }


    public static function validationRules(): array
    {
        return [
            'data.shop_id'           => 'required|string',
            'data.secret_key'        => 'required|string',
            'data.callback'          => 'required|string',
            'data.test'              => 'required|in:0,1',
            'data.price'             => 'nullable',
            'data.short_description' => 'array',
            'data.description'       => 'array',
        ];
    }


    /** Controller će pozvati ovo da pripremi $data za blade */
    public static function buildFrontData(array $provider, array $ctx): array
    {
        $cfg    = array_replace_recursive(static::defaultConfig(), $provider);
        $isTest = (int) ($cfg['test'] ?? 1) === 1;

        $action = $cfg['gateway'][$isTest ? 'test' : 'live'] ?? static::defaultConfig()['gateway'][$isTest ? 'test' : 'live'];

        // format iz starog koda
        $rawOrderId = (string) ($ctx['order_id'] ?? '');
        $orderId    = $rawOrderId . '-' . date('Y');

        [$totalDisplay, $totalForHash] = static::formatTotal($ctx['total'] ?? 0);

        $shopId    = (string) ($cfg['shop_id'] ?? '');
        $secretKey = (string) ($cfg['secret_key'] ?? '');

        $signature = md5(
            $shopId
            . $secretKey
            . $orderId
            . $secretKey
            . $totalForHash
            . $secretKey
        );

        $c = (array) ($ctx['customer'] ?? []);

        return [
            'action'   => $action,
            'shop_id'  => $shopId,
            'order_id' => $orderId,
            'total'    => $totalDisplay,
            'md5'      => $signature,

            'firstname' => (string) ($c['firstname'] ?? ''),
            'lastname'  => (string) ($c['lastname'] ?? ''),
            'address'   => (string) ($c['address'] ?? ''),
            'city'      => (string) ($c['city'] ?? ''),
            'country'   => (string) ($c['country'] ?? 'HR'),
            'postcode'  => (string) ($c['postcode'] ?? ''),
            'phone'     => (string) ($c['phone'] ?? ''),
            'email'     => (string) ($c['email'] ?? ''),

            'lang'     => strtoupper((string) ($ctx['lang'] ?? 'HR')),
            'return'   => (string) ($ctx['return'] ?? ($cfg['callback'] ?? url('/'))),
            'cancel'   => (string) ($ctx['cancel'] ?? ($cfg['callback'] ?? url('/'))),
            'currency' => (string) ($cfg['currency'] ?? 'EUR'),
            'method'   => 'POST',
        ];
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
