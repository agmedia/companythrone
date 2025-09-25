<?php

namespace App\Services\Settings;

use App\Models\Back\Settings\Settings as SettingsModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

class SettingsManager
{
    protected string $cachePrefix = 'settings:';

    public function get(string $code, string $key, mixed $default = null): mixed
    {
        $cacheKey = $this->cachePrefix.$code.':'.$key;

        return Cache::rememberForever($cacheKey, function () use ($code, $key, $default) {
            $row = SettingsModel::query()->where('code', $code)->where('key', $key)->first();
            if (!$row) return $default;

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

        Cache::forget($this->cachePrefix.$code.':'.$key);
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
}
