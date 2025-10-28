<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Category;
use App\Models\Back\Catalog\Company;
use Illuminate\Http\Request;

class CompanyListController extends Controller
{

    /**
     * Lista tvrtki s filtriranjem po kategoriji, pretragom i sortiranjem.
     * Oslanja se na company_translations / category_translations i aktivni locale.
     */
    /*public function index(Request $request)
    {
        $locale = app()->getLocale();

        $q    = (string) $request->string('q')->toString();
        $cat  = (string) $request->string('category')->toString(); // category slug za aktivni jezik
        $sort = (string) $request->string('sort', 'newest')->toString();

        $companies = Company::query()
                            ->join('company_translations as t', function ($join) use ($locale) {
                                $join->on('t.company_id', '=', 'companies.id')
                                     ->where('t.locale', '=', $locale);
                            })
                            ->select([
                                'companies.*',
                                't.name as t_name',
                                't.slug as t_slug',
                                't.slogan',
                                't.description',
                            ])
                            ->when($q, function ($query) use ($q) {
                                $like = '%' . str_replace('%', '\%', $q) . '%';
                                $query->where(function ($q2) use ($like) {
                                    $q2->where('t.name', 'like', $like)
                                       ->orWhere('companies.oib', 'like', $like)
                                       ->orWhere('companies.city', 'like', $like)
                                       ->orWhere('companies.state', 'like', $like);
                                });
                            })
                            ->when($cat, function ($query) use ($cat, $locale) {
                                $query->join('category_company as cc', 'cc.company_id', '=', 'companies.id')
                                      ->join('category_translations as ct', function ($j) use ($locale) {
                                          $j->on('ct.category_id', '=', 'cc.category_id')
                                            ->where('ct.locale', '=', $locale);
                                      })
                                      ->where('ct.slug', '=', $cat);
                            })
                            ->where('companies.is_published', true)
                            ->when($sort, function ($query) use ($sort) {
                                switch ($sort) {
                                    case 'name_asc':
                                        $query->orderBy('t.name', 'asc');
                                        break;
                                    case 'name_desc':
                                        $query->orderBy('t.name', 'desc');
                                        break;
                                    case 'random':
                                        $query->inRandomOrder();
                                        break;
                                    case 'newest':
                                    default:
                                        $query->orderBy('companies.created_at', 'desc');
                                        break;
                                }
                            })
                            ->paginate(12)
                            ->appends([
                                'q'        => $q,
                                'category' => $cat,
                                'sort'     => $sort,
                            ]);

        // Kategorije za dropdown (aktivni jezik)
        $categories = Category::query()
                              ->join('category_translations as ct', function ($j) use ($locale) {
                                  $j->on('ct.category_id', '=', 'categories.id')
                                    ->where('ct.locale', '=', $locale);
                              })
                              ->where('categories.is_active', true)
                              ->select(['categories.id', 'ct.name', 'ct.slug','categories.group'])
                              ->orderBy('ct.name')
                              ->get();


        return view('front.companies.index', [
            'companies'  => $companies,
            'categories' => $categories,
            'q'          => $q,
            'cat'        => $cat,
            'sort'       => $sort,
        ]);
    }*/

    public function index(Request $request)
    {
        $locale = app()->getLocale();

        $q     = trim((string) $request->input('q', ''));
        $cat   = trim((string) $request->input('category', ''));
        $sort  = trim((string) $request->input('sort', 'newest'));
        $mode  = in_array($request->input('mode'), ['name','oib','tag'], true)
            ? $request->input('mode')
            : 'name';

        $companies = Company::query()
            // LEFT JOIN da ne isključi tvrtke bez prijevoda
            ->leftJoin('company_translations as t', function ($join) use ($locale) {
                $join->on('t.company_id', '=', 'companies.id')
                    ->where('t.locale', '=', $locale);
            })
            ->select([
                'companies.*',
                't.name as t_name',
                't.slug as t_slug',
                't.slogan',
                't.description',
            ])

            // 🔎 Pretraga po odabranom načinu (bez miješanja između modova)
            ->when($q && $mode === 'oib', function ($query) use ($q) {
                // Zadrži samo znamenke iz unosa
                $digits = preg_replace('/[^0-9]/', '', $q);

                // Mora imati točno 11 znamenki i proći kontrolnu znamenku
                if (strlen($digits) !== 11 || !self::isValidOib($digits)) {
                    // Nevažeći OIB -> nema rezultata
                    $query->whereRaw('1=0');
                    return;
                }

                // Točno podudaranje OIB-a (bez parcijalnog LIKE-a)
                $query->where('companies.oib', $digits);
            })

            ->when($q && $mode === 'name', function ($query) use ($q) {
                $like = '%' . str_replace('%', '\%', $q) . '%';
                $query->where('t.name', 'like', $like);
            })

            ->when($q && $mode === 'tag', function ($query) use ($q) {
                $clean = preg_replace('/[\s,]+/', ' ', trim($q));

                // Pretvori upit u boolean fulltext (npr. "web marketing" -> "+web* +marketing*")
                $terms = array_filter(explode(' ', $clean));
                $boolean = implode(' ', array_map(function ($t) {
                    // očisti svaki token od spec. znakova; dopusti slova/brojeve/underscore/crticu
                    $t = preg_replace('/[^[:alnum:]\p{L}\p{N}_-]/u', '', $t);
                    return $t !== '' ? '+' . $t . '*' : '';
                }, $terms));

                $query->where(function ($q2) use ($boolean, $q) {
                    if ($boolean !== '') {
                        $q2->whereRaw('MATCH (companies.keywords) AGAINST (? IN BOOLEAN MODE)', [$boolean]);
                    }
                    // Fallback na LIKE (ako nema FT pogodaka ili je upit kratak)
                    $q2->orWhere('companies.keywords', 'like', '%' . $q . '%');
                });
            })

            // 🔹 Filtar po kategoriji
            ->when($cat, function ($query) use ($cat, $locale) {
                $query->join('category_company as cc', 'cc.company_id', '=', 'companies.id')
                    ->join('category_translations as ct', function ($j) use ($locale) {
                        $j->on('ct.category_id', '=', 'cc.category_id')
                            ->where('ct.locale', '=', $locale);
                    })
                    ->where('ct.slug', '=', $cat);
            })

            // 🔹 Samo objavljene tvrtke
            ->where('companies.is_published', true)

            // 🔹 Sortiranje
            ->when($sort, function ($query) use ($sort) {
                switch ($sort) {
                    case 'name_asc':
                        $query->orderBy('t.name', 'asc');
                        break;
                    case 'name_desc':
                        $query->orderBy('t.name', 'desc');
                        break;
                    case 'random':
                        $query->inRandomOrder();
                        break;
                    case 'newest':
                    default:
                        $query->orderBy('companies.created_at', 'desc');
                        break;
                }
            })

            ->paginate(12)
            ->appends([
                'q'        => $q,
                'category' => $cat,
                'sort'     => $sort,
                'mode'     => $mode,
            ]);

        // 🔹 Kategorije za dropdown
        $categories = Category::query()
            ->join('category_translations as ct', function ($j) use ($locale) {
                $j->on('ct.category_id', '=', 'categories.id')
                    ->where('ct.locale', '=', $locale);
            })
            ->where('categories.is_active', true)
            ->select(['categories.id', 'ct.name', 'ct.slug', 'categories.group'])
            ->orderBy('ct.name')
            ->get();

        return view('front.companies.index', [
            'companies'  => $companies,
            'categories' => $categories,
            'q'          => $q,
            'cat'        => $cat,
            'sort'       => $sort,
            'mode'       => $mode, // za UI (checked state)
        ]);
    }

    /**
     * Provjera HR OIB-a (mod 11,10).
     * Vraća true samo ako je 11 znamenki i kontrolna znamenka ispravna.
     */
    private static function isValidOib(string $oib): bool
    {
        if (!preg_match('/^\d{11}$/', $oib)) {
            return false;
        }

        $a = 10;
        for ($i = 0; $i < 10; $i++) {
            $a = ($a + (int) $oib[$i]) % 10;
            if ($a === 0) {
                $a = 10;
            }
            $a = ($a * 2) % 11;
        }
        $control = (11 - $a) % 10;

        return $control === (int) $oib[10];
    }




    /**
     * Detalj tvrtke po lokaliziranom slug-u (company_translations.slug).
     * Ovdje ne koristimo implicitni binding jer je slug po jeziku.
     */
    public function show(Company $company)
    {
        $locale = app()->getLocale();

        /*$company = Company::query()
                          ->join('company_translations as t', function ($join) use ($locale) {
                              $join->on('t.company_id', '=', 'companies.id')
                                   ->where('t.locale', '=', $locale);
                          })
                          ->where('t.slug', '=', $slug)
                          ->select([
                              'companies.*',
                              't.name as t_name',
                              't.slug as t_slug',
                              't.slogan',
                              't.description',
                          ])
                          ->firstOrFail();*/

        //dd($company->categories()->get()->toArray());

        // (Opcionalno) učitaj pripadne kategorije za ovaj jezik – korisno za breadcrumb/tagove
        $categories = Category::query()
                              ->join('category_company as cc', 'cc.category_id', '=', 'categories.id')
                              ->join('category_translations as ct', function ($j) use ($locale) {
                                  $j->on('ct.category_id', '=', 'categories.id')
                                    ->where('ct.locale', '=', $locale);
                              })
                              ->where('cc.company_id', '=', $company->id)
                              ->select(['categories.id', 'ct.name', 'ct.slug'])
                              ->orderBy('ct.name')
                              ->get();

        $featured = Company::query()->where('is_published', true)->where('id', '!=', $company->id)->latest()->take(12)->get();

        // View `front.company-show` već koristi fallback pattern (t_name ?? name)

        return view('front.company-show', [
            'company'    => $company,
            'categories' => $categories,
            'featured'   => $featured,
        ]);
    }
}
