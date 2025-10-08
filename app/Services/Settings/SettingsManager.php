<?php

namespace App\Services\Settings;

use App\Models\Back\Settings\Settings as SettingsModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsManager
{

    protected string $cachePrefix = 'settings:';


    public function get(string $code, string $key, mixed $default = null): mixed
    {
        $cacheKey = $this->cachePrefix . $code . ':' . $key;

        return Cache::rememberForever($cacheKey, function () use ($code, $key, $default) {
            $row = SettingsModel::query()->where('code', $code)->where('key', $key)->first();
            if ( ! $row) {
                return $default;
            }

            if ($row->json) {
                $decoded = json_decode($row->value, true);

                return $decoded === null ? $default : $decoded;
            }

            return $row->value ?? $default;
        });
    }


    public function set(string $code, string $key, mixed $value, bool $asJson = false): void
    {
        // upsert bez “array wrap” bugova
        $payload = [
            'code'  => $code,
            'key'   => $key,
            'value' => $asJson ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string) $value,
            'json'  => $asJson ? 1 : 0,
        ];

        $row = SettingsModel::query()->where('code', $code)->where('key', $key)->first();

        if ($row) {
            $row->update($payload);
        } else {
            SettingsModel::query()->create($payload);
        }

        Cache::forget($this->cachePrefix . $code . ':' . $key);
    }


    public function setMany(array $grouped): void
    {
        // $grouped = ['ui' => ['admin_pagination' => 20, ...], 'site' => ['title' => ['hr'=>'...','en'=>'...']]]
        foreach ($grouped as $code => $pairs) {
            foreach ($pairs as $key => $val) {
                $asJson = is_array($val) || is_object($val);
                $this->set($code, $key, $val, $asJson);
            }
        }
    }


    /**
     * Vrati SVE aktivne payment opcije iz DB (settings), normalizirane i obogaćene podacima iz config providera.
     * Struktura retka:
     * [
     *   code, name, price, currency, period, short_description, description,
     *   provider (ako postoji u configu), driver (FQCN) , sort_order, geo_zone, min, data (sirovi),
     * ]
     */
    public function paymentsActive(?string $locale = null): array
    {
        $locale ??= app()->getLocale();

        // 1) Config provideri (enabled)
        $providers = collect(config('settings.payments.providers', []))
            ->filter(fn($p) => Arr::get($p, 'enabled', false))
            ->mapWithKeys(function ($p, $code) use ($locale) {
                $driver       = Arr::get($p, 'driver');
                $defaultTitle = method_exists($driver, 'defaultTitle') ? (array) $driver::defaultTitle() : [];
                $fallbackName = $p['name'] ?? ucfirst($code);

                return [
                    $code => [
                        'provider_code'  => $code,
                        'status'         => $p['enabled'],
                        'driver'         => $driver,
                        'name_default'   => $defaultTitle[$locale] ?? $defaultTitle['en'] ?? $fallbackName,
                        'config_default' => method_exists($driver, 'defaultConfig') ? (array) $driver::defaultConfig() : [],
                    ]
                ];
            });

        // 2) Redovi iz DB-a (key='payment', json=1) —> value = JSON array stavki
        $rows = DB::table('settings')
                  ->where('code', 'payments')
                  ->where('json', 1) // ⬅️ ako koristiš drugi stupac za flag, promijeni ovdje
                  ->orderBy('id')
                  ->get(['value'/* ⬅️ ovo je tvoj JSON */, 'key', 'code']); // dohvatimo i path ako ti zatreba

        $items = collect();
        $db = collect();

        foreach ($rows as $row) {
            $decoded = json_decode($row->value); // ⬅️ ako je JSON u drugom stupcu, promijeni ovdje

            if (is_array($decoded)) {
                $decoded = collect($decoded)->first();
            }

            $code  = (string) ($decoded->code ?? '');
            $data  = (array) ($decoded->config ?? []);
            $pconf = $providers->get($code, null);

            // title može biti plain string; ako je array, uzmi lokalizirano
            $title = $decoded->title ?? null;
            if (is_array($title)) {
                $name = $title->{$locale} ?? $title->en ?? reset($title);
            } else {
                $name = $title ?: ($pconf['name_default'] ?? ucfirst($code));
            }

            $short = $decoded->short_description ?? null;
            if (is_array($short)) {
                $short = $short->{$locale} ?? $short->en ?? reset($short);
            }

            $descr = $decoded->description ?? null;
            if (is_array($descr)) {
                $descr = $descr->{$locale} ?? $descr->en ?? reset($descr);
            }

            $db->push([
                'code'              => $code,
                'name'              => $name,
                'price'             => $this->castPrice($data->price ?? null),
                'currency'          => $data->currency ?? 'EUR',
                'period'            => $data->period ?? 'oneoff', // ili 'monthly' ako tako vodiš
                'short_description' => $short,
                'description'       => $descr,
                'min'               => $data->min ?? null,
                'geo_zone'          => $data->geo_zone ?? null,
                'sort_order'        => (int) ($decoded->sort_order ?? 0),
                'status'            => (bool) ($decoded->status ?? true),
                'provider'          => $code,                 // default pretpostavka: code == provider
                'provider_exists'   => (bool) $pconf,
                'driver'            => $pconf['driver'] ?? null,
                'data'              => $data,
            ]);
        }

        // 3) Ako u DB-u nema ničega, ipak izlistaj enable-ane providere iz configa kao “fallback”
        if ($providers->isNotEmpty()) {
            $items = $providers->keys()->map(function ($code) use ($providers, $db) {
                $p = $providers[$code];
                $row = $db->where('code', $code)->first();

                if ($row && $row['status'] === true && $p['status'] === true) {
                    return [
                        'code'              => $code,
                        'name'              => $row['name'],
                        'price'             => $row['price'] ?? 0,
                        'currency'          => $row['currency'] ?? 'EUR',
                        'period'            => $p['config_default']['period'] ?? 'oneoff',
                        'short_description' => isset($row['short_description'][current_locale()]) ? $row['short_description'][current_locale()] : $row['short_description'],
                        'description'       => Arr::get($p['config_default'], 'description'),
                        'min'               => $row['min'],
                        'geo_zone'          => $row['geo_zone'],
                        'sort_order'        => $row['sort_order'] ?? 0,
                        'status'            => true,
                        'provider'          => $code,
                        'provider_exists'   => true,
                        'driver'            => $p['driver'],
                        'data'              => $row['data'],
                    ];
                }
            })->values();
        }

        // 4) Posloži i deduplikacija po code
        $items = $items
            ->unique('code')->whereNotNull()
            ->sortBy([
                ['sort_order', 'asc'],
                ['price', 'asc'],
                ['code', 'asc'],
            ])
            ->values();

        return $items->all();
    }


    /** Vrati jednu payment opciju po code-u (ili null ako je nema / disabled). */
    public function paymentByCode(string $code, ?string $locale = null): ?array
    {
        $code = (string) $code;
        $all  = $this->paymentsActive($locale);
        foreach ($all as $p) {
            if ($p['code'] === $code) {
                return $p;
            }
        }

        return null;
    }




    /* ===== helpers ===== */

    private function castPrice($val): float|int
    {
        if (is_null($val) || $val === '') {
            return 0;
        }
        if (is_numeric($val)) {
            return $val + 0;
        }
        // “5,00” → 5.00
        $normalized = str_replace(['.', ','], ['', '.'], preg_replace('/[^\d,\.]/', '', (string) $val));

        return is_numeric($normalized) ? ($normalized + 0) : 0;
    }

}
