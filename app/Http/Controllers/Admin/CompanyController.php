<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\StoreCompanyRequest;
use App\Http\Requests\Back\UpdateCompanyRequest;
use App\Models\Back\Catalog\Company;
use App\Models\Back\Catalog\CompanyTranslation;
use App\Models\Back\Catalog\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
class CompanyController extends Controller
{

    /*public function dashboard()
    {
        return view('admin.dashboard');
    }*/

    public function index()
    {
        $companies = Company::latest()->paginate(20);

        return view('admin.catalog.companies.index', compact('companies'));
    }


    public function create()
    {
        $levels = $this->levelsForSelect();

        return view('admin.catalog.companies.edit', compact('levels'));
    }


    public function store(StoreCompanyRequest $request)
    {
        $company = Company::create($request->baseData());

        // Spremi prijevode
        foreach ($request->translationsData() as $tr) {
            CompanyTranslation::updateOrCreate(
                ['company_id' => $company->id, 'locale' => $tr['locale']],
                Arr::except($tr, ['locale']) + ['company_id' => $company->id]
            );
        }

        $company->categories()->sync($request->input('category_ids', []));

        if ($request->hasFile('logo_file')) {
            $company->addMediaFromRequest('logo_file')->toMediaCollection('logo');
        }
        if ($request->hasFile('banner_file')) {
            $company->addMediaFromRequest('banner_file')->toMediaCollection('banner');
        }

        return redirect()->route('catalog.companies.index')->with('success', 'Company created.');
    }


    public function edit(Company $company)
    {
        $company->load('translations');
        $levels = $this->levelsForSelect();

        return view('admin.catalog.companies.edit', compact('company', 'levels'));
    }


    public function update(UpdateCompanyRequest $request, Company $company)
    {

       // dd($request->validated()['name'] ?? null);
        $company->update($request->baseData());

        foreach ($request->translationsData() as $tr) {
            CompanyTranslation::updateOrCreate(
                ['company_id' => $company->id, 'locale' => $tr['locale']],
                Arr::except($tr, ['locale']) + ['company_id' => $company->id]
            );
        }

        $company->categories()->sync($request->input('category_ids', []));

        // LOGO
        if ($request->boolean('remove_logo')) {
            $company->clearMediaCollection('logo');
        } elseif ($request->hasFile('logo_file')) {
            $company->addMediaFromRequest('logo_file')->toMediaCollection('logo'); // singleFile => auto-replace
        }

        // BANNER
        if ($request->boolean('remove_banner')) {
            $company->clearMediaCollection('banner');
        } elseif ($request->hasFile('banner_file')) {
            $company->addMediaFromRequest('banner_file')->toMediaCollection('banner');
        }

        return redirect()->route('catalog.companies.index')->with('success', 'Company updated.');
    }


    public function destroy(Company $company)
    {
        $company->delete();

        return back()->with('success', 'Company deleted.');
    }


    private function levelsForSelect(): array
    {
        return Level::query()
                    ->orderBy('id') // ili position ako postoji
                    ->get()
                    ->mapWithKeys(fn($l) => [$l->id => $l->label])
                    ->all();
    }
}
