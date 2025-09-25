<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Api\V1\Settings\Base\AbstractSettingsProviderController;

class ShippingController extends AbstractSettingsProviderController
{
    protected string $sectionCode = 'shipping';
    protected array  $cacheKeys   = ['shipping_list'];
}
