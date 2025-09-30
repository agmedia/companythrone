<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function edit()
    {
        $company = auth()->user()->company;

        return view('front.account.company', compact('company'));
    }

    public function update(Request $request)
    {
        $company = auth()->user()->company;

        if (! $company) {
            abort(404, __('Tvrtka nije pronađena.'));
        }

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'oib'        => ['required', 'string', 'max:20'],
            'email'      => ['required', 'email', 'max:255'],
            'website'    => ['required', 'url', 'max:255'],
            'street'     => ['nullable', 'string', 'max:255'],
            'street_no'  => ['nullable', 'string', 'max:50'],
            'city'       => ['nullable', 'string', 'max:255'],
            'state'      => ['nullable', 'string', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:50'],
            'description'=> ['nullable', 'string'],
            'logo'       => ['nullable', 'image', 'max:2048'],
        ]);

        // upload loga ako je poslan
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('company-logos', 'public');
            $validated['logo'] = $path;
        }

        $company->update($validated);

        return redirect()
            ->route('account.company.edit', app()->getLocale())
            ->with('status', __('Podaci o tvrtki su uspješno ažurirani.'));
    }
}
