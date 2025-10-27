<?php

namespace App\Http\Controllers\Front\Account;

use App\Http\Controllers\Controller;
use App\Models\Back\Catalog\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            'logo_file'   => ['nullable', 'image', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'keywords'    => ['nullable', 'string', 'max:255'],
        ]);

        // Validacija keywords — ručno jer Laravel ne zna za broj riječi po zarezima
        $keywords = null;
        if (! empty($validated['keywords'])) {
            $parts = array_filter(array_map('trim', explode(',', $validated['keywords'])));

            if (count($parts) > 5) {
                return back()
                    ->withErrors(['keywords' => __('Maksimalno 5 ključnih riječi, odvojenih zarezom.')])
                    ->withInput();
            }

            foreach ($parts as $kw) {
                if (strlen($kw) > 30) {
                    return back()
                        ->withErrors(['keywords' => __('Svaka ključna riječ može imati najviše 30 znakova.')])
                        ->withInput();
                }
            }

            // Ako je sve ok, spoji ih natrag kao string
            $keywords = implode(',', $parts);
        }

        DB::transaction(function () use ($company, $validated, $keywords) {

            $company->fill([
                'oib'         => $validated['oib'],
                'email'       => $validated['email'],
                'weburl'      => $validated['weburl'],
                'street'      => $validated['street'] ?? null,
                'street_no'   => $validated['street_no'] ?? null,
                'city'        => $validated['city'] ?? null,
                'state'       => $validated['state'] ?? null,
                'phone'       => $validated['phone'] ?? null,
                'description' => $validated['description'] ?? null,
                'keywords'    => $keywords,
            ]);
            $company->save();

            if (method_exists($company, 'translations')) {
                $locale = app()->getLocale();
                $t      = $company->translation($locale) ?? $company->translations()->make(['locale' => $locale]);

                $t->name = $validated['name'];
                if (! empty($validated['description'])) {
                    $t->description = $validated['description'];
                }

                if (empty($t->slug)) {
                    $t->slug = Str::slug($t->name) ?: (string) $company->getKey();
                }

                $company->translations()->save($t);
            } else {
                $company->name = $validated['name'];
                if (empty($company->slug)) {
                    $company->slug = Str::slug($company->name) ?: (string) $company->getKey();
                }
                $company->save();
            }
        });

        if ($request->boolean('remove_logo')) {
            $company->clearMediaCollection('logo');
        } elseif ($request->hasFile('logo_file')) {
            if (! $request->file('logo_file')->isValid()) {
                return back()->withErrors(['logo_file' => __('Neispravan upload datoteke (provjeri veličinu i tip).')])->withInput();
            }
            $company->addMediaFromRequest('logo_file')->toMediaCollection('logo');
        }

        return redirect()
            ->route('account.company.edit', app()->getLocale())
            ->with('status', __('Podaci o tvrtki su uspješno ažurirani.'));
    }


}
