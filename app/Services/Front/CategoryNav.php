<?php

namespace App\Services\Front;

use App\Models\Admin\Catalog\Category as AdminCategory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class CategoryNav
{

    private string $locale;


    public function __construct(?string $locale = null)
    {
        $this->locale = $locale ?: App::getLocale();
    }


    /** Public helpers za tipične grupe */
    public function companies(bool $onlyNav = true, ?int $depth = null): array
    {
        return $this->tree('tvrtke', $onlyNav, $depth);
    }


    public function blog(bool $onlyNav = true, ?int $depth = 1): array
    {
        // blog obično plitko u navigaciji
        return $this->tree('blog', $onlyNav, $depth);
    }


    public function pages(bool $onlyNav = true, ?int $depth = 1): array
    {
        // info-stranice (group=pages), često flat/top-level
        return $this->tree('pages', $onlyNav, $depth);
    }


    public function footer(bool $onlyNav = true, ?int $depth = null): array
    {
        return $this->tree('footer', $onlyNav, $depth);
    }


    /**
     * Glavna metoda: vrati navigacijsko stablo po grupi.
     * - $onlyNav: filtrira na is_navbar=1
     * - $depth: maksimalna dubina (null = bez limita)
     */
    public function tree(string $group, bool $onlyNav = true, ?int $depth = null): array
    {
        $q = AdminCategory::query()
                          ->forGroup($group)              // već zove defaultOrder()
                          ->where('is_active', true)
                          ->with(['translations', 'media']); // eager-load da izbjegnemo N+1

        if ($onlyNav) {
            $q->where('is_navbar', true);
        }

        if ($depth !== null) {
            $q->withDepth()->having('depth', '<=', $depth);
        }

        $tree = $q->get()->toTree();

        $map = function (AdminCategory $node) use (&$map) {
            $data = $this->toFrontNode($node);
            if ($node->children && $node->children->isNotEmpty()) {
                $data['children'] = $node->children->map(fn($c) => $map($c))->all();
            }

            return $data;
        };

        return $tree->map(fn($n) => $map($n))->all();
    }


    /** Flatten helper ako negdje treba lista umjesto stabla */
    public function flat(string $group, bool $onlyNav = true, ?int $maxDepth = 1): array
    {
        $nodes = $this->tree($group, $onlyNav, $maxDepth);
        $out   = [];
        $walk  = function (array $items) use (&$walk, &$out) {
            foreach ($items as $it) {
                $copy             = $it;
                $copy['children'] = [];
                $out[]            = $copy;
                if ( ! empty($it['children'])) {
                    $walk($it['children']);
                }
            }
        };
        $walk($nodes);

        return $out;
    }


    // app/Services/Front/CategoryNav.php

    public function groupIndexUrl(string $group, ?string $locale = null): string
    {
        $locale    ??= $this->locale;
        $groupSlug = $this->groupSlug($group, $locale);

        return url("/{$locale}/{$groupSlug}");
    }


    /**
     * Vrati URL za info-stranicu (group=pages) po slug-u.
     * - $validate=true: provjerava postoji li aktivan zapis s tim slugom (za zadani ili fallback locale).
     *   Ako ne postoji, vrati null.
     */
    public function pageUrl(string $slug, ?string $locale = null, bool $validate = true): ?string
    {
        return $this->urlFor(group: 'pages', slug: $slug, locale: $locale, validate: $validate);
    }


    /**
     * Vrati URL za kategoriju po slug-u unutar grupe.
     * Ako $validate=true, provjerava da postoji aktivna kategorija s tim slugom (locale → fallback).
     */
    public function urlFor(string $group, string $slug, ?string $locale = null, bool $validate = true): ?string
    {
        $locale ??= $this->locale;

        if ($validate) {
            $exists = \App\Models\Admin\Catalog\Category::query()
                                                        ->forGroup($group)
                                                        ->where('is_active', true)
                                                        ->whereHas('translations', fn($q) => $q->where('locale', $locale)->where('slug', $slug))
                                                        ->exists();

            if ( ! $exists) {
                $fallback = Config::get('app.fallback_locale');
                $exists   = \App\Models\Admin\Catalog\Category::query()
                                                              ->forGroup($group)
                                                              ->where('is_active', true)
                                                              ->whereHas('translations', fn($q) => $q->where('locale', $fallback)->where('slug', $slug))
                                                              ->exists();

                if ( ! $exists) {
                    return null;
                }
            }
        }

        $groupSlug = $this->groupSlug($group, $locale);

        return url("/{$locale}/{$groupSlug}/{$slug}");
    }


    /**
     * Vrati URL za kategoriju po ID-u (uzima slug iz prijevoda).
     * Vraća null ako zapis ne postoji ili je neaktivan.
     */
    public function urlById(int $id, ?string $locale = null): ?string
    {
        $locale ??= $this->locale;

        $cat = \App\Models\Admin\Catalog\Category::query()
                                                 ->whereKey($id)
                                                 ->where('is_active', true)
                                                 ->with('translations')
                                                 ->first();

        if ( ! $cat) {
            return null;
        }

        $t    = $cat->translation($locale);
        $slug = $t?->slug ?: (string) $cat->id;

        $groupSlug = $this->groupSlug($cat->group, $locale);

        return url("/{$locale}/{$groupSlug}/{$slug}");
    }


    /* ================= Interno ================= */

    private function toFrontNode(AdminCategory $cat): array
    {
        $t = $cat->translation($this->locale);

        return [
            'id'         => $cat->id,
            'group'      => $cat->group,
            'title'      => $t?->title ?? '—',
            'slug'       => $t?->slug,
            'url'        => $this->makeUrl($cat, $t?->slug),
            'is_active'  => (bool) $cat->is_active,
            'is_navbar'  => (bool) $cat->is_navbar,

            // Front-flagovi po grupi (bez mijenjanja modela/tablica)
            'is_classic' => in_array($cat->group, ['tvrtke', 'blog'], true),
            'is_page'    => $cat->group === 'pages',

            // Spatie media (opcionalno, null ako nema)
            'media'      => [
                'icon'   => $cat->getFirstMediaUrl('icon', 'thumb') ?: $cat->getFirstMediaUrl('icon') ?: null,
                'image'  => $cat->getFirstMediaUrl('image') ?: null,
                'banner' => $cat->getFirstMediaUrl('banner') ?: null,
            ],

            'children' => [],
        ];
    }


    private function makeUrl(AdminCategory $cat, ?string $slug): string
    {
        $slug      = $slug ?: (string) $cat->id;
        $groupSlug = $this->groupSlug($cat->group, $this->locale);

        // Klasične kategorije i pages imaju isti obrazac: /{locale}/{groupSlug}/{slug}
        return url("/{$this->locale}/{$groupSlug}/{$slug}");
    }


    private function groupSlug(string $group, string $locale): string
    {
        // npr. settings.group_slugs.tvrtke.hr = 'tvrtke', .en = 'companies'
        return config("settings.group_slugs.{$group}.{$locale}", $group);
    }
}
