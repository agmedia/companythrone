<?php

namespace App\Payments\Contracts;

use App\Models\Back\Catalog\Company;
use Illuminate\Http\Request;

interface PaymentProviderInterface
{

    public static function code(): string;


    public static function defaultTitle(): array;   // ['hr'=>'..','en'=>'..']


    public static function defaultConfig(): array;  // provider-specific fields


    public static function validateResponse(Request $request, Company $company): array;


    public static function buildFrontData(array $data): array;


    public static function frontBlade(): string;    // front partial path
}
