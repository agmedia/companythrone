<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

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
            'name'        => ['required', 'string', 'max:255'],
            'oib'         => ['required', 'string', 'max:20'],
            'email'       => ['required', 'email', 'max:255'],
            'weburl'      => ['required', 'url', 'max:255'],
            'street'      => ['nullable', 'string', 'max:255'],
            'street_no'   => ['nullable', 'string', 'max:50'],
            'city'        => ['nullable', 'string', 'max:255'],
            'state'       => ['nullable', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'logo_file'        => ['nullable', 'image', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
        ]);

        // 1) Update osnovnih polja bez 'logo'
        //$company->update(Arr::except($validated, ['logo_file']));

        // 2) LOGO MediaLibrary
        if ($request->boolean('remove_logo')) {
            $company->clearMediaCollection('logo');
        } elseif ($request->hasFile('logo_file')) {

            // Ako PHP limit “pojede” file, hasFile će biti false — pa ovo NIJE ni pokrenuto.
            // Ako dođe do ove linije, dodatno provjeri valjanost uploada:
            if (! $request->file('logo_file')->isValid()) {
                return back()->withErrors(['logo_file' => __('Neispravan upload datoteke (provjeri veličinu i tip).')])->withInput();
            }

            // singleFile() će automatski zamijeniti stari logo
            $company->addMediaFromRequest('logo_file')->toMediaCollection('logo');
        }

        return redirect()
            ->route('account.company.edit', app()->getLocale())
            ->with('status', __('Podaci o tvrtki su uspješno ažurirani.'));
    }

}
