<?php

namespace App\Http\Controllers\Admin\Settings\Local;

use App\Http\Controllers\Admin\Settings\Base\AbstractSettingsListPageController;

class TaxPageController extends AbstractSettingsListPageController
{
    protected string $sectionCode = 'tax';
    protected string $view        = 'admin.settings.local.taxes';
}
