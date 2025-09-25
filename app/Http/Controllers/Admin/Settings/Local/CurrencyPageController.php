<?php

namespace App\Http\Controllers\Admin\Settings\Local;

use App\Http\Controllers\Admin\Settings\Base\AbstractSettingsListPageController;
use Illuminate\Support\Collection;

class CurrencyPageController extends AbstractSettingsListPageController
{
    protected string $sectionCode = 'currency';
    protected string $view        = 'admin.settings.local.currency';

    protected function extras(Collection $items): array
    {
        $main = $items->firstWhere('main', true);
        return ['main' => $main];
    }
}
