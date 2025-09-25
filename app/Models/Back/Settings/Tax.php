<?php

namespace App\Models\Back\Settings;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Tax
{
    public static function all(): Collection
    {
        return Cache::rememberForever('tax_list', fn () => Settings::get('tax', 'list'));
    }

    public static function main(): ?object
    {
        return self::all()->firstWhere('main', true);
    }
}