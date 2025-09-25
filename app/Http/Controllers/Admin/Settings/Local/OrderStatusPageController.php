<?php

namespace App\Http\Controllers\Admin\Settings\Local;

use App\Http\Controllers\Admin\Settings\Base\AbstractSettingsListPageController;

class OrderStatusPageController extends AbstractSettingsListPageController
{
    protected string $sectionCode = 'order_statuses';
    protected string $view        = 'admin.settings.local.order-statuses';
}
