<?php

namespace App\Services\Settings;

use App\Models\Back\Settings\Settings as SettingsModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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
                        'driver'         => $driver,
                        'name_default'   => $defaultTitle[$locale] ?? $defaultTitle['en'] ?? $fallbackName,
                        'config_default' => method_exists($driver, 'defaultConfig') ? (array) $driver::defaultConfig() : [],
                    ]
                ];
            });

        // 2) Redovi iz DB-a (key='payment', json=1) —> value = JSON array stavki
        $rows = DB::table('settings')
                  ->where('key', 'payment')
                  ->where('json', 1) // ⬅️ ako koristiš drugi stupac za flag, promijeni ovdje
                  ->orderBy('id')
                  ->get(['value'/* ⬅️ ovo je tvoj JSON */, 'key', 'code']); // dohvatimo i path ako ti zatreba

        $items = collect();

        foreach ($rows as $row) {
            $decoded = json_decode($row->value ?? '[]', true); // ⬅️ ako je JSON u drugom stupcu, promijeni ovdje
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                continue;
            }
            foreach ($decoded as $entry) {
                if ( ! is_array($entry)) {
                    continue;
                }

                // status filter
                if (array_key_exists('status', $entry) && ! $entry['status']) {
                    continue;
                }

                $code  = (string) ($entry['code'] ?? '');
                $data  = (array) ($entry['data'] ?? []);
                $pconf = $providers->get($code, null);

                // title može biti plain string; ako je array, uzmi lokalizirano
                $title = $entry['title'] ?? null;
                if (is_array($title)) {
                    $name = $title[$locale] ?? $title['en'] ?? reset($title);
                } else {
                    $name = $title ?: ($pconf['name_default'] ?? ucfirst($code));
                }

                $short = $data['short_description'] ?? null;
                if (is_array($short)) {
                    $short = $short[$locale] ?? $short['en'] ?? reset($short);
                }

                $descr = $data['description'] ?? null;
                if (is_array($descr)) {
                    $descr = $descr[$locale] ?? $descr['en'] ?? reset($descr);
                }

                $items->push([
                    'code'              => $code,
                    'name'              => $name,
                    'price'             => $this->castPrice($data['price'] ?? null),
                    'currency'          => $data['currency'] ?? 'EUR',
                    'period'            => $data['period'] ?? 'oneoff', // ili 'monthly' ako tako vodiš
                    'short_description' => $short,
                    'description'       => $descr,
                    'min'               => $entry['min'] ?? null,
                    'geo_zone'          => $entry['geo_zone'] ?? null,
                    'sort_order'        => (int) ($entry['sort_order'] ?? 0),
                    'status'            => (bool) ($entry['status'] ?? true),
                    'provider'          => $code,                 // default pretpostavka: code == provider
                    'provider_exists'   => (bool) $pconf,
                    'driver'            => $pconf['driver'] ?? null,
                    'data'              => $data,
                ]);
            }
        }

        // 3) Ako u DB-u nema ničega, ipak izlistaj enable-ane providere iz configa kao “fallback”
        if ($items->isEmpty() && $providers->isNotEmpty()) {
            $items = $providers->keys()->map(function ($code) use ($providers) {
                $p = $providers[$code];

                return [
                    'code'              => $code,
                    'name'              => $p['name_default'],
                    'price'             => $p['config_default']['price'] ?? 0,
                    'currency'          => $p['config_default']['currency'] ?? 'EUR',
                    'period'            => $p['config_default']['period'] ?? 'oneoff',
                    'short_description' => Arr::get($p['config_default'], 'short_description'),
                    'description'       => Arr::get($p['config_default'], 'description'),
                    'min'               => null,
                    'geo_zone'          => null,
                    'sort_order'        => 0,
                    'status'            => true,
                    'provider'          => $code,
                    'provider_exists'   => true,
                    'driver'            => $p['driver'],
                    'data'              => $p['config_default'],
                ];
            })->values();
        }

        // 4) Posloži i deduplikacija po code
        $items = $items
            ->unique('code')
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
