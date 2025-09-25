<?php

namespace App\Payments\Providers\Cod;

use App\Payments\Contracts\PaymentProviderInterface;

class Driver implements PaymentProviderInterface
{
    public static function code(): string { return 'cod'; }

    public static function defaultTitle(): array
    {
        return ['hr' => 'Pouzeće', 'en' => 'Cash on Delivery'];
    }

    public static function defaultConfig(): array
    {
        return [
            'instructions' => [
                'hr' => 'Plaćanje gotovinom pri preuzimanju.',
                'en' => 'Pay with cash upon delivery.',
            ],
        ];
    }

    public static function validationRules(): array
    {
        return [
            'instructions.hr' => 'nullable|string',
            'instructions.en' => 'nullable|string',
        ];
    }

    public static function frontBlade(): string
    {
        return 'front.checkout.payments.cod';
    }
}
