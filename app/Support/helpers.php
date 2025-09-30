<?php

use App\Models\Back\Catalog\Company;
use App\Services\Front\CategoryNav;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

if ( ! function_exists('localized_route')) {
    /**
     * Build a locale-prefixed URL for a named route.
     *
     * @param string      $name   Route name
     * @param mixed       $params Array|Model|scalar params
     * @param string|null $locale Force locale (null = current)
     */
    function localized_route(string $name, mixed $params = [], ?string $locale = null): string
    {
        if ($params instanceof Model || is_scalar($params)) {
            $params = [$params];       // let route() resolve single param / route key
        } elseif ( ! is_array($params)) {
            $params = Arr::wrap($params);
        }

        // false → relative path (so LL can prepend /hr or /en)
        $relative = route($name, $params, false);

        return LaravelLocalization::getLocalizedURL($locale, $relative);
    }
}

if ( ! function_exists('uniqueLocalizedSlug')) {
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
        $try  = $slug;
        $i    = 2;

        while (\Illuminate\Support\Facades\DB::table($table)->where('locale', $locale)->where('slug', $try)->exists()) {
            $try = $slug . '-' . $i++;
        }

        return $try;
    }
}

/**
 *
 */
if ( ! function_exists('current_locale')) {
    /**
     * @param bool $native
     *
     * @return string
     */
    function current_locale(bool $native = false): string
    {
        $current = app()->getLocale();

        if ($native) {
            return config('laravellocalization.supportedLocales.' . $current . '.regional');
        }

        return $current;
    }
}

if ( ! function_exists('nav')) {
    /**
     * Quick access na CategoryNav servis.
     *
     * // Info stranica "o nama"
     * @urlAbout = nav()->pageUrl('o-nama');       // /hr/info-stranica/o-nama
     *
     * // Bilo koja grupa + slug
     * @urlBlog  = nav()->urlFor('blog', 'novosti');
     *
     * // Po ID-u (uzima slug iz prijevoda)
     * @urlCat   = nav()->urlById(42);
     *
     * // EN varijanta
     * @urlEn    = nav('en')->urlFor('tvrtke', 'automotive');
     *
     * @param string|null $locale Ako je null, uzima se App::getLocale()
     */
    function nav(?string $locale = null): CategoryNav
    {
        return is_null($locale)
            ? app(CategoryNav::class)
            : app()->makeWith(CategoryNav::class, ['locale' => $locale]);
    }
}

if ( ! function_exists('nav_url')) {
    /**
     * Brzi helper za URL (kategorije ili pages).
     * Primjeri:
     *  - nav_url('pages', 'o-nama')
     *  - nav_url('blog', 'novosti')
     *  - nav_url('tvrtke', id: 42)
     */
    function nav_url(string $group, ?string $slug = null, ?int $id = null, ?string $locale = null, bool $validate = true): ?string
    {
        $svc = nav($locale);

        if ($id) {
            return $svc->urlById($id, $locale);
        }

        if ($group === 'pages') {
            return $slug ? $svc->pageUrl($slug, $locale, $validate) : null;
        }

        return $slug ? $svc->urlFor($group, $slug, $locale, $validate) : null;
    }
}

if ( ! function_exists('nav_group_url')) {
    /**
     * Dohvaća URL za grupu na temelju zadanog jezika i grupe.
     *
     * @param string      $group  Naziv grupe za koju se generira URL.
     * @param string|null $locale Jezik na kojem se generira URL.
     *                            Ako je null, koristi se zadani jezik aplikacije.
     *
     * @return string Generirani URL za zadanu grupu.
     */
    function nav_group_url(string $group, ?string $locale = null): string
    {
        return nav($locale)->groupIndexUrl($group);
    }
}


if (!function_exists('company_url')) {
    /**
     * Generates a localized URL for a company.
     *
     * Determines the slug based on the provided company instance or the passed string/integer value.
     * If a `Company` object is given, its translated or fallback slug is used.
     * If the input is a string or integer, it is treated directly as a slug.
     *
     * @param Company|string|int $company The company instance, or a string/int representing the slug or ID.
     *
     * @return string The localized URL for the company.
     */
    function company_url($company, ?string $locale = null): string
    {
        $locale ??= App::getLocale();

        // Ako je model, izvuci slug iz više mogućih mjesta. Nikad ne šalji cijeli model u rutu.
        if ($company instanceof Company) {
            $slug =
                // accessor / atribut t_slug ako postoji
                data_get($company, 't_slug')
                // klasični slug kolona
                ?? data_get($company, 'slug')
                   // prijevodni slug (ako imaš translation($locale))
                   ?? optional(method_exists($company, 'translation') ? $company->translation($locale) : null)->slug
                      // fallback na ID
                      ?? (string) ($company->getKey() ?? $company->id);
        } else {
            // ako je već string/int, tretiraj kao slug
            $slug = (string) $company;
        }

        // Na kraju: uvijek proslijedi samo scalar parametar
        return localized_route('companies.show', ['companyBySlug' => (string) $slug]);
    }
}