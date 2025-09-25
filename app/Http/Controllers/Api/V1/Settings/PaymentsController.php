<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Api\V1\Settings\Base\AbstractSettingsProviderController;

class PaymentsController extends AbstractSettingsProviderController
{
    protected string $sectionCode = 'payments';
    protected array  $cacheKeys   = ['payments_list'];
}
