<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Company;
use Illuminate\Http\Request;

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
        return view('admin.catalog.companies.create');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'title'  => ['required', 'string', 'max:255'],
            'slug'   => ['required', 'string', 'max:255', 'unique:companies,slug'],
            'group'  => ['nullable', 'string', 'max:100'],
            'clicks' => ['nullable', 'integer'],
            'active' => ['boolean'],
        ]);

        Company::create($data);

        return redirect()->route('companies.index')->with('success', 'Company created.');
    }


    public function edit(Company $company)
    {
        return view('admin.catalog.companies.edit', compact('company'));
    }


    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'title'  => ['required', 'string', 'max:255'],
            'slug'   => ['required', 'string', 'max:255', 'unique:companies,slug,' . $company->id],
            'group'  => ['nullable', 'string', 'max:100'],
            'clicks' => ['nullable', 'integer'],
            'active' => ['boolean'],
        ]);

        $company->update($data);

        return redirect()->route('companies.index')->with('success', 'Company updated.');
    }


    public function destroy(Company $company)
    {
        $company->delete();

        return back()->with('success', 'Company deleted.');
    }
}
