<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Api\V1\Settings\Base\AbstractSettingsListController;

class CurrencyController extends AbstractSettingsListController
{
    protected string $sectionCode = 'currency';
    protected array  $cacheKeys   = ['currency_list','currency_main','currency_secondary'];

    protected function normalizeItem(array $d, ?object $existing = null): object
    {
        return (object)[
            'id'             => (int)($d['id'] ?? 0),
            'title'          => (object)($d['title'] ?? []),
            'code'           => strtoupper(trim((string)($d['code'] ?? ''))),
            'symbol_left'    => $d['symbol_left']  ?? null,
            'symbol_right'   => $d['symbol_right'] ?? null,
            'value'          => isset($d['value']) ? (float)$d['value'] : null,
            'decimal_places' => (int)($d['decimal_places'] ?? 2),
            'status'         => (bool)($d['status'] ?? false),
            'main'           => (bool)($d['main'] ?? false),
            'sort_order'     => (int)($d['sort_order'] ?? 0),
        ];
    }

    protected function afterListMutate(\Illuminate\Support\Collection $items, object $saved): \Illuminate\Support\Collection
    {
        if (!empty($saved->main)) {
            $items = $items->map(function ($it) use ($saved) {
                $it->main = ((int)$it->id === (int)$saved->id);
                return $it;
            });
        }
        return $items;
    }
}
