<?php

namespace App\Payments\Contracts;

interface PaymentProviderInterface
{
    public static function code(): string;
    public static function defaultTitle(): array;   // ['hr'=>'..','en'=>'..']
    public static function defaultConfig(): array;  // provider-specific fields
    public static function validationRules(): array;
    public static function frontBlade(): string;    // front partial path
}
