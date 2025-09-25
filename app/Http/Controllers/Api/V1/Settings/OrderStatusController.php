<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Api\V1\Settings\Base\AbstractSettingsListController;

class OrderStatusController extends AbstractSettingsListController
{
    protected string $sectionCode = 'order_statuses';

    protected function normalizeItem(array $d, ?object $existing = null): object
    {
        return (object)[
            'id'         => (int)($d['id'] ?? 0),
            'title'      => (object)($d['title'] ?? []),
            'color'      => (string)($d['color'] ?? 'light'),
            'sort_order' => (int)($d['sort_order'] ?? 0),
            'status'     => true, // statuses are always "usable"; keep if you expose toggle
        ];
    }
}
