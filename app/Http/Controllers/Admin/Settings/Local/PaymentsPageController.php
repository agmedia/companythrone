<?php

namespace App\Http\Controllers\Admin\Settings\Local;

use App\Http\Controllers\Admin\Settings\Base\AbstractProvidersPageController;

class PaymentsPageController extends AbstractProvidersPageController
{
    protected string $sectionCode         = 'payments';
    protected string $view                = 'admin.settings.local.payments';
    protected string $configProvidersPath = 'settings.payments.providers';
    protected bool   $needsGeoZones       = true; // COD/Bank/WSPay etc. may need it
}
