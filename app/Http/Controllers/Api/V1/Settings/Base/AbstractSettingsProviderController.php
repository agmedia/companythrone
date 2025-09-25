<?php

namespace App\Http\Controllers\Api\V1\Settings\Base;

use App\Http\Controllers\Controller;
use App\Models\Back\Settings\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class AbstractSettingsProviderController extends Controller
{
    /** e.g. 'payments', 'shipping' */
    protected string $sectionCode = 'providers';

    /** Cache keys to flush (override) */
    protected array $cacheKeys = [];

    protected function allRows(): Collection
    {
        return Settings::where('code', $this->sectionCode)
                       ->where('key', '<>', 'list')
                       ->get();
    }

    protected function findRow(string $key): ?Settings
    {
        return Settings::where('code', $this->sectionCode)->where('key', $key)->first();
    }

    protected function writeOne(string $key, array $payload): bool
    {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($row = $this->findRow($key)) {
            return Settings::edit($row->id, $this->sectionCode, $key, $json, true);
        }
        return Settings::insert($this->sectionCode, $key, $json, true);
    }

    protected function deleteOne(string $key): bool
    {
        return (bool) Settings::where('code', $this->sectionCode)->where('key', $key)->delete();
    }

    protected function cleanCache(): void
    {
        foreach ($this->cacheKeys as $key) {
            cache()->forget($key);
        }
    }

    // ------- public endpoints -------

    public function index()
    {
        $items = $this->allRows()
                      ->map(function ($row) {
                          $val = json_decode($row->value) ?: (object)[];
                          return (object)[
                              'id'         => (int)($val->id ?? 0),
                              'code'       => (string)($val->code ?? $row->key),
                              'title'      => (object)($val->title ?? []),
                              'sort_order' => (int)($val->sort_order ?? 0),
                              'status'     => (bool)($val->status ?? true),
                              'config'     => (object)($val->config ?? []),
                          ];
                      })
                      ->sortBy('sort_order')
                      ->values();

        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $this->cleanCache();

        $d = $request->input('data', []);
        if (!is_array($d)) return response()->json(['message' => 'Invalid payload'], 422);

        $code = strtolower(trim((string)($d['code'] ?? '')));
        if ($code === '') return response()->json(['message' => 'Missing provider code'], 422);

        $all   = $this->allRows();
        $maxId = $all->map(fn($r) => (int)((json_decode($r->value)->id ?? 0)))->max() ?: 0;

        $incomingId = (int)($d['id'] ?? 0);
        $existing   = $this->findRow($code);
        $keepId     = $existing ? ((int)(json_decode($existing->value)->id ?? 0)) : 0;

        $id         = $incomingId > 0 ? $incomingId : ($keepId ?: ($maxId + 1));
        $payload    = [
            'id'         => $id,
            'code'       => $code,
            'title'      => (object)($d['title'] ?? []),
            'sort_order' => (int)($d['sort_order'] ?? 0),
            'status'     => (bool)($d['status'] ?? true),
            'config'     => (object)($d['config'] ?? []),
        ];

        return $this->writeOne($code, $payload)
            ? response()->json(['success' => true])
            : response()->json(['message' => 'Save failed'], 422);
    }

    public function destroy(Request $request)
    {
        $this->cleanCache();

        $id   = (int)($request->input('data.id') ?? 0);
        $code = (string)($request->input('data.code') ?? '');

        if ($code === '' && $id > 0) {
            $row = $this->allRows()->first(function ($r) use ($id) {
                $val = json_decode($r->value);
                return $val && (int)($val->id ?? 0) === $id;
            });
            if ($row) $code = $row->key;
        }

        if ($code === '') return response()->json(['message' => 'Missing provider'], 422);

        return $this->deleteOne($code)
            ? response()->json(['success' => true])
            : response()->json(['message' => 'Delete failed'], 422);
    }
}
