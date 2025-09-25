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


    public static function frontBlade(): string
    {
        // not used now; kept to satisfy interface
        return 'front.checkout.payment.wspay';
    }
}
