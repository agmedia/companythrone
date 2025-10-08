<?php

namespace App\Http\Controllers\Admin\Settings\Base;

use App\Http\Controllers\Controller;
use App\Models\Back\Settings\Settings;
use Illuminate\Support\Collection;

abstract class AbstractProvidersPageController extends Controller
{
    /** Settings.code (e.g. 'payments' or 'shipping') */
    protected string $sectionCode = '';

    /** View path (e.g. 'back.settings.shop.payments') */
    protected string $view = '';

    /** Config providers path (e.g. 'settings.payments.providers') */
    protected string $configProvidersPath = '';

    /** When true, pass $geo_zones to the view (read from settings: geozones/list) */
    protected bool $needsGeoZones = true;

    /** Child can inject extras to the view payload */
    protected function extras(Collection $items): array
    {
        return [];
    }

    /** Read all provider rows from DB: one row per provider (key=<provider code>) */
    protected function readDbProviders(): Collection
    {
        return Settings::where('code', $this->sectionCode)
                       ->where('key', '<>', 'list') // ensure per-provider rows
                       ->get()
                       ->mapWithKeys(function ($row) {
                           $val  = json_decode($row->value) ?: (object)[];
                           $code = (string)($val->code ?? $row->key);
                           return [$code => (object)[
                               'id'         => (int)($val->id ?? 0),
                               'code'       => $code,
                               'title'      => (object)($val->title ?? []),
                               'sort_order' => (int)($val->sort_order ?? 0),
                               'status'     => (bool)($val->status ?? true),
                               'config'     => (object)($val->config ?? []),
                           ]];
                       });
    }

    /** Build synthesized item for a provider present in config but missing in DB */
    protected function synthesizeFromConfig(string $code, array $meta): object
    {
        $driver = $meta['driver'] ?? null;
        $title  = [$code => ucfirst($code)];
        $config = [];

        if ($driver && class_exists($driver)) {
            if (method_exists($driver, 'defaultTitle')) {
                try { $title = (array) $driver::defaultTitle(); } catch (\Throwable $e) {}
            }
            if (method_exists($driver, 'defaultConfig')) {
                try { $config = (array) $driver::defaultConfig(); } catch (\Throwable $e) {}
            }
        }

        return (object)[
            'id'         => 0, // not persisted yet
            'code'       => $code,
            'title'      => (object)$title,
            'sort_order' => 1000, // push to end until user saves
            'status'     => (bool)($meta['enabled'] ?? true),
            'config'     => (object)$config,
        ];
    }

    /** Merge DB rows with providers declared in config */
    protected function mergedProviders(): Collection
    {
        $db  = $this->readDbProviders(); // map(code => row)
        $cfg = collect(config($this->configProvidersPath, [])); // map(code => meta)

        $items = collect();

        // Add config-declared providers; prefer DB row if present
        foreach ($cfg as $code => $meta) {
            $code = (string)$code;
            if ($db->has($code)) {
                $items->push($db->get($code));
            } else {
                $items->push($this->synthesizeFromConfig($code, (array)$meta));
            }
        }

        // Keep DB-only providers too (not present in config)
        $cfgCodes = $cfg->keys()->map(fn($k) => (string)$k)->all();
        $db->each(function ($row, $code) use ($cfgCodes, $items) {
            if (!in_array($code, $cfgCodes, true)) {
                $items->push($row);
            }
        });

        // Sort by sort_order then code
        return $items
            ->sort(fn ($a, $b) => ($a->sort_order <=> $b->sort_order) ?: strcmp($a->code, $b->code))
            ->values();
    }

    public function index()
    {
        $items   = $this->mergedProviders()->where('id', '!=', 0);
        $locales = config('shop.locales', ['hr' => 'Hrvatski', 'en' => 'English']);

        $payload = [
            'items'   => $items,
            'locales' => $locales,
            'providers' => collect(config($this->configProvidersPath, [])),
        ];

        if ($this->needsGeoZones) {
            $geoRow    = Settings::where('code', 'geozones')->where('key', 'list')->first();
            $geo_zones = collect($geoRow ? json_decode($geoRow->value) : [])->map(fn($it) => (object)$it);
            $payload['geo_zones'] = $geo_zones;
        }

        return view($this->view, array_merge($payload, $this->extras($items)));
    }
}
