<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Api\V1\Settings\Base\AbstractSettingsListController;

class GeozoneController extends AbstractSettingsListController
{
    protected string $sectionCode = 'geozones';

    protected function normalizeItem(array $d, ?object $existing = null): object
    {
        $countries = array_values(array_map('intval', $d['countries'] ?? []));
        $zones     = array_values(array_map('intval', $d['zones'] ?? []));

        // build legacy "state" map of id => name (ids can be country or zone)
        $state = (object)($d['state'] ?? []); // if frontend already provided map, keep it
        if (empty((array)$state)) {
            $state = (object)[];
            // you can fill from lookups if needed (left empty here to avoid unintended deletes)
        }

        return (object)[
            'id'          => (int)($d['id'] ?? 0),
            'title'       => (object)($d['title'] ?? []),
            'description' => $d['description'] ?? null,
            'status'      => (bool)($d['status'] ?? true),
            'countries'   => $countries, // optional normalized fields
            'zones'       => $zones,     // optional normalized fields
            'state'       => $state,     // legacy consumer compatibility
            'sort_order'  => (int)($d['sort_order'] ?? 0),
        ];
    }
}
