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
