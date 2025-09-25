<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Api\V1\Settings\Base\AbstractSettingsListController;

class TaxController extends AbstractSettingsListController
{
    protected string $sectionCode = 'tax';

    protected function normalizeItem(array $d, ?object $existing = null): object
    {
        return (object)[
            'id'         => (int)($d['id'] ?? 0),
            'title'      => (object)($d['title'] ?? []),
            'rate'       => (string)($d['rate'] ?? ''),   // keep as string if you allow comma, etc.
            'sort_order' => (int)($d['sort_order'] ?? 0),
            'status'     => (bool)($d['status'] ?? true),
            'geo_zone'   => isset($d['geo_zone']) ? (int)$d['geo_zone'] : null,
        ];
    }
}
