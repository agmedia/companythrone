<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Admin\Catalog\Category;
use App\Models\Company;

// prilagodi ako je namespace drukčiji
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class CategoryController extends Controller
{

    public function index(Request $request, string $groupSlug)
    {
        $locale = app()->getLocale(); // ← uzmi locale ovako
        $group  = $this->canonicalGroup($groupSlug, $locale);
        if (!$group) abort(404);

        $roots = Category::query()
                         ->forGroup($group)                 // ->defaultOrder()
                         ->where('is_active', true)
                         ->with(['translations', 'media'])
                         ->withDepth()->having('depth', '=', 0)
                         ->get();

        return view($group === 'pages' ? 'front.pages.index' : 'front.categories.index', [
            'group'     => $group,
            'groupSlug' => $groupSlug,
            'locale'    => $locale,
            'roots'     => $roots,
        ]);
    }


    public function show(Request $request, string $groupSlug, string $slug)
    {
        $locale = app()->getLocale(); // ← ovdje također
        $group  = $this->canonicalGroup($groupSlug, $locale);
        if (!$group) abort(404);

        // 1) pokušaj na tren. jeziku
        $category = $this->findByLocalizedSlug($group, $slug, $locale);

        // 2) fallback jezik -> 301 redirect na kanonski URL
        if ( ! $category) {
            $fallback    = Config::get('app.fallback_locale', 'en');
            $fallbackHit = $this->findByLocalizedSlug($group, $slug, $fallback);

            if ($fallbackHit) {
                $canonicalSlug = optional($fallbackHit->translation($locale))->slug ?: (string) $fallbackHit->id;

                return redirect()->to($this->buildUrl($locale, $group, $canonicalSlug), 301);
            }
            abort(404);
        }

        // breadcrumbs kao kolekcija Category modela (točno onako kako tvoj blade očekuje)
        $trail = $category->ancestors()   // obrati pažnju na zagrade!
                          ->defaultOrder()
                          ->get();

        $trail->push($category);         // dodaj aktivnu stranicu

        // djeca (za "Subcategories" sekciju)
        $children = $category->children()
                             ->where('is_active', true)
                             ->with(['translations', 'media'])
                             ->defaultOrder()
                             ->get();

        // pages: info stranica
        if ($group === 'pages') {
            $t = $category->translation($locale);

            //dd($trail);

            return view('front.pages.show', [
                'category'    => $category,
                'breadcrumbs' => $trail,
                'title'       => $t?->name ?? '',
                // prilagodi naziv polja teksta ovisno o tvojoj tablici (text / content / body)
                'content'     => $t->description ?? $t->content ?? $t->body ?? null,
            ]);
        }

        // tvrtke (klasična kategorija): dohvat firmi iz kategorije + potomci
        $ids = $category->descendants()->pluck('id')->push($category->id);

        $companiesQuery = Company::query()
                                 ->where('is_published', true)
            // očekuje se relacija categories() na Company modelu
                                 ->whereHas('categories', fn($q) => $q->whereIn('categories.id', $ids));

        // ako imaš drugačiji pivot ili relaciju, zamijeni gornji whereHas odgovarajućim joinom

        $companies = $companiesQuery
            ->latest('published_at')
            ->paginate(18);

        return view('front.categories.show', [
            'category'    => $category,
            'breadcrumbs' => $trail,
            'children'    => $children,
            'companies'   => $companies,
        ]);
    }


    /* ================= Helpers ================= */

    private function canonicalGroup(string $groupSlug, string $locale): ?string
    {
        $map = config('settings.group_slugs', []);
        foreach ($map as $group => $perLocale) {
            $localized = $perLocale[$locale] ?? $group;
            if ($localized === $groupSlug) {
                return $group;
            }
        }

        return array_key_exists($groupSlug, $map) ? $groupSlug : null;
    }


    private function findByLocalizedSlug(string $group, string $slug, string $locale): ?Category
    {
        return Category::query()
                       ->forGroup($group)
                       ->where('is_active', true)
                       ->whereHas('translations', fn($q) => $q->where('locale', $locale)->where('slug', $slug))
                       ->with(['translations', 'media'])
                       ->first();
    }


    private function buildUrl(string $locale, string $group, string $slug): string
    {
        $groupSlug = config("settings.group_slugs.{$group}.{$locale}", $group);

        return url("/{$locale}/{$groupSlug}/{$slug}");
    }
}
