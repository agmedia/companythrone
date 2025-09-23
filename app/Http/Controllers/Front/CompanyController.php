<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{

    public function show(Company $company)
    {

        $featured = Company::query()->where('is_published', true)->latest()->take(12)->get();

        return view('front.company-show', compact('company','featured'));
    }


    public function create()
    {
        return view('front.company-create');
    }

    public function payment()
    {
        return view('front.company-payment');
    }

    public function review()
    {
        return view('front.company-review');
    }

    public function success()
    {
        return view('front.company-success');
    }


    public function store(Request $request)
    {
      /*  $data    = $request->validate([
            'name'  => 'required|string|max:255', 'oib' => 'required|string|max:20|unique:companies,oib',
            'email' => 'required|email|unique:companies,email', 'street' => 'nullable|string', 'street_no' => 'nullable|string',
            'city'  => 'nullable|string', 'state' => 'nullable|string', 'phone' => 'nullable|string', 'logo' => 'nullable|image'
        ]);
        $company = \App\Models\Back\Catalog\Company::create($data);
        if ($request->hasFile('logo')) {
            $company->addMediaFromRequest('logo')->toMediaCollection('logo');
        } */

// TODO: kreiraj ponudu 25€/god i poslati e-mail
      //  return redirect()->route('companies.show', $company)->with('success', __('company.created'));

        //return redirect()->route('companies.payment', $company);
        return redirect()->route('companies.payment');
    }
}
