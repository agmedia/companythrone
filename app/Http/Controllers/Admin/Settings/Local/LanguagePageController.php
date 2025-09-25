<?php

namespace App\Http\Controllers\Admin\Settings\Local;

use App\Http\Controllers\Admin\Settings\Base\AbstractSettingsListPageController;
use Illuminate\Support\Collection;

class LanguagePageController extends AbstractSettingsListPageController
{
    protected string $sectionCode = 'language';
    protected string $view        = 'admin.settings.local.languages';

    protected function extras(Collection $items): array
    {
        $main = $items->firstWhere('main', true);
        return ['main' => $main];
    }
}
