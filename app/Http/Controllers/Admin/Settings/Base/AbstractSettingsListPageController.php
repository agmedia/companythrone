<?php

namespace App\Http\Controllers\Admin\Settings\Base;

use App\Http\Controllers\Controller;
use App\Models\Back\Settings\Settings;
use Illuminate\Support\Collection;

abstract class AbstractSettingsListPageController extends Controller
{
    /** e.g. 'language', 'currency', 'tax', 'geozones', 'order_statuses' */
    protected string $sectionCode = 'settings';

    /** blade view path, e.g. 'back.settings.shop.languages' */
    protected string $view = '';

    /** Allow child to tweak sorting */
    protected function sort(Collection $items): Collection
    {
        if ($items->first() && isset($items->first()->sort_order)) {
            return $items->sortBy('sort_order')->values();
        }
        return $items->sortBy('id')->values();
    }

    protected function readAll(): Collection
    {
        $row = Settings::where('code', $this->sectionCode)->where('key', 'list')->first();
        $data = $row ? json_decode($row->value) : [];
        $arr  = is_array($data) ? $data : [];
        return collect($arr)->map(fn($it) => is_object($it) ? $it : (object)$it);
    }

    /** Overridable “extras” passed to the view */
    protected function extras(Collection $items): array
    {
        return [];
    }

    public function index()
    {
        $items   = $this->sort($this->readAll());
        $locales = config('shop.locales', ['hr' => 'Hrvatski', 'en' => 'English']);

        return view($this->view, array_merge([
            'items'   => $items,
            'locales' => $locales,
        ], $this->extras($items)));
    }
}
