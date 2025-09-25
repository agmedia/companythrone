<?php

namespace App\Payments\Providers\Bank;

use App\Payments\Contracts\PaymentProviderInterface;

class Driver implements PaymentProviderInterface
{
    public static function code(): string { return 'bank'; }

    public static function defaultTitle(): array
    {
        return ['hr' => 'Virman / Bankovna uplata', 'en' => 'Bank Transfer'];
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

    public static function validationRules(): array
    {
        return [
            'account_name'     => 'required|string',
            'iban'             => 'required|string',
            'swift'            => 'nullable|string',
            'bank_name'        => 'nullable|string',
            'instructions.hr'  => 'nullable|string',
            'instructions.en'  => 'nullable|string',
        ];
    }

    public static function frontBlade(): string
    {
        return 'front.checkout.payments.bank';
    }
}
