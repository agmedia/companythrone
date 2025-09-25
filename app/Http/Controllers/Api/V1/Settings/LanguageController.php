<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Api\V1\Settings\Base\AbstractSettingsListController;
use Illuminate\Http\Request;

class LanguageController extends AbstractSettingsListController
{
    protected string $sectionCode = 'language';
    protected array  $cacheKeys   = ['language_list', 'language_main'];

    protected function normalizeItem(array $d, ?object $existing = null): object
    {
        return (object)[
            'id'     => (int)($d['id'] ?? 0),
            'title'  => (object)($d['title'] ?? []),
            'code'   => strtoupper(trim((string)($d['code'] ?? ''))),
            'status' => (bool)($d['status'] ?? false),
            'main'   => (bool)($d['main'] ?? false),
        ];
    }

    protected function afterListMutate(\Illuminate\Support\Collection $items, object $saved): \Illuminate\Support\Collection
    {
        // ensure single main
        if (!empty($saved->main)) {
            $items = $items->map(function ($it) use ($saved) {
                $it->main = ((int)$it->id === (int)$saved->id);
                return $it;
            });
        }
        return $items;
    }
}
