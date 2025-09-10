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
    public function index(Request $request)
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
                              ->select(['categories.id', 'ct.name', 'ct.slug'])
                              ->orderBy('ct.name')
                              ->get();

        return view('front.companies.index', [
            'companies'  => $companies,
            'categories' => $categories,
            'q'          => $q,
            'cat'        => $cat,
            'sort'       => $sort,
        ]);
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

        // View `front.company-show` već koristi fallback pattern (t_name ?? name)
        return view('front.company-show', [
            'company'    => $company,
            'categories' => $categories,
        ]);
    }
}
