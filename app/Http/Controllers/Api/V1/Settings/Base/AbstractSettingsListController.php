<?php

namespace App\Http\Controllers\Api\V1\Settings\Base;

use App\Http\Controllers\Controller;
use App\Models\Back\Settings\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class AbstractSettingsListController extends Controller
{
    /** e.g. 'language', 'currency', 'tax', 'geozones', 'order_statuses' */
    protected string $sectionCode = 'settings';

    /** Cache keys to flush (override if needed) */
    protected array $cacheKeys = [];

    /** Read the single list row */
    protected function row(): ?Settings
    {
        return Settings::where('code', $this->sectionCode)->where('key', 'list')->first();
    }

    /** Read list as collection of objects */
    protected function readAll(): Collection
    {
        $row = $this->row();
        $data = $row ? json_decode($row->value) : [];
        $items = is_array($data) ? $data : [];
        return collect($items)->map(function ($it) {
            // normalize to object
            return is_object($it) ? $it : (object)$it;
        });
    }

    /** Persist the entire list (collection|array of objects) */
    protected function writeAll($items): bool
    {
        $json = collect($items)->values()->toJson(JSON_UNESCAPED_UNICODE);
        if ($row = $this->row()) {
            return Settings::edit($row->id, $this->sectionCode, 'list', $json, true);
        }
        return Settings::insert($this->sectionCode, 'list', $json, true);
    }

    /** Sort helper (override per resource if needed) */
    protected function sort(Collection $items): Collection
    {
        if ($items->first() && isset($items->first()->sort_order)) {
            return $items->sortBy('sort_order')->values();
        }
        return $items->sortBy('id')->values();
    }

    /** Hook to normalize incoming item (override in child) */
    protected function normalizeItem(array $d, ?object $existing = null): object
    {
        return (object)[
            'id'         => (int)($d['id'] ?? 0),
            'title'      => (object)($d['title'] ?? []),
            'sort_order' => (int)($d['sort_order'] ?? 0),
            'status'     => (bool)($d['status'] ?? true),
        ];
    }

    /** Optional post-mutation hook (e.g., ensure single "main") */
    protected function afterListMutate(Collection $items, object $justSaved): Collection
    {
        return $items;
    }

    /** Flush caches */
    protected function cleanCache(): void
    {
        foreach ($this->cacheKeys as $key) {
            cache()->forget($key);
        }
    }

    // -------- public endpoints --------

    public function index()
    {
        return response()->json([
            'items' => $this->sort($this->readAll())->values(),
        ]);
    }

    public function store(Request $request)
    {
        $this->cleanCache();

        $d = $request->input('data', []);
        if (!is_array($d)) {
            return response()->json(['message' => 'Invalid payload'], 422);
        }

        $items = $this->readAll();

        $incomingId = (int)($d['id'] ?? 0);
        $existing   = $incomingId > 0 ? $items->firstWhere('id', $incomingId) : null;
        $normalized = $this->normalizeItem($d, $existing);

        if ($incomingId === 0) {
            $normalized->id = ((int)$items->max('id')) + 1;
            $items->push($normalized);
        } else {
            $items = $items->map(function ($it) use ($normalized) {
                if ((int)$it->id === (int)$normalized->id) return $normalized;
                return $it;
            });
        }

        $items = $this->afterListMutate($items, $normalized);

        return $this->writeAll($items)
            ? response()->json(['success' => true])
            : response()->json(['message' => 'Save failed'], 422);
    }

    public function destroy(Request $request)
    {
        $this->cleanCache();

        $id = (int)($request->input('data.id') ?? 0);
        if ($id <= 0) return response()->json(['message' => 'Missing id'], 422);

        $items = $this->readAll()->reject(fn($it) => (int)$it->id === $id)->values();

        return $this->writeAll($items)
            ? response()->json(['success' => true])
            : response()->json(['message' => 'Delete failed'], 422);
    }
}
