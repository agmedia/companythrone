<?php

namespace App\Models\Back\Settings;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Language
{
    public static function all(): Collection
    {
        return Cache::rememberForever('language_list', fn () => Settings::get('language', 'list'));
    }

    public static function main(): ?object
    {
        return self::all()->firstWhere('main', true);
    }
}