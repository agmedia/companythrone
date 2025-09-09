<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

if (! function_exists('localized_route')) {
    /**
     * Build a locale-prefixed URL for a named route.
     *
     * @param  string      $name    Route name
     * @param  mixed       $params  Array|Model|scalar params
     * @param  string|null $locale  Force locale (null = current)
     */
    function localized_route(string $name, mixed $params = [], ?string $locale = null): string
    {
        if ($params instanceof Model || is_scalar($params)) {
            $params = [$params];       // let route() resolve single param / route key
        } elseif (! is_array($params)) {
            $params = Arr::wrap($params);
        }

        // false → relative path (so LL can prepend /hr or /en)
        $relative = route($name, $params, false);

        return LaravelLocalization::getLocalizedURL($locale, $relative);
    }
}

if (! function_exists('uniqueLocalizedSlug')) {
    /**
     * Generates a unique localized slug for a specified database table.
     *
     * This function creates a slug from the given base string and ensures its uniqueness
     * within the specified table and locale. If a slug with the same locale and value already exists,
     * a numerical suffix is appended to make it unique.
     *
     * @param string $table  The name of the database table to check for slug uniqueness.
     * @param string $locale The locale to be considered while generating the unique slug.
     * @param string $base   The base string to be converted into a slug.
     *
     * @return string A unique slug value for the given table and locale.
     */
    function uniqueLocalizedSlug(string $table, string $locale, string $base): string
    {
        $slug = \Illuminate\Support\Str::slug($base);
        $try = $slug;
        $i = 2;

        while (\Illuminate\Support\Facades\DB::table($table)->where('locale',$locale)->where('slug',$try)->exists()) {
            $try = $slug.'-'.$i++;
        }
        return $try;
    }
}
