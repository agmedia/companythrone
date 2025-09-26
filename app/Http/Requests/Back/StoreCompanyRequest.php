<?php

namespace App\Http\Requests\Back;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $locales = array_keys(config('app.locales', ['hr' => 'Hrvatski', 'en' => 'English']));

        $rules = [
            // Core (companies)
            'level_id'        => ['required', 'integer', 'exists:levels,id'],
            'oib'             => ['required', 'string', 'max:20', 'unique:companies,oib'],
            'street'          => ['nullable', 'string', 'max:255'],
            'street_no'       => ['nullable', 'string', 'max:50'],
            'city'            => ['nullable', 'string', 'max:120'],
            'state'           => ['nullable', 'string', 'max:120'],
            'email'           => ['required', 'email', 'max:255', 'unique:companies,email'],
            'weburl'          => ['nullable', 'string', 'max:255', 'url', 'unique:companies,weburl'],
            'phone'           => ['nullable', 'string', 'max:80'],
            'is_published'    => ['boolean'],
            'is_link_active'  => ['boolean'],
            'referrals_count' => ['nullable', 'integer', 'min:0'],
            'clicks'          => ['nullable', 'integer', 'min:0'],
            'published_at'    => ['nullable', 'date'],

            // Arrays exist
            'name'        => ['required', 'array'],
            'slug'        => ['required', 'array'],
            'slogan'      => ['nullable', 'array'],
            'description' => ['nullable', 'array'],

            // Files
            'logo_file'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:5120'],
            'banner_file'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:8192'],
            'remove_logo'   => ['sometimes', 'boolean'],
            'remove_banner' => ['sometimes', 'boolean'],
        ];

        // Per-locale rules
        foreach ($locales as $code) {
            $rules["name.$code"] = ['required', 'string', 'max:255'];
            $rules["slug.$code"] = [
                'required', 'string', 'max:255',
                Rule::unique('company_translations', 'slug')->where('locale', $code),
            ];
            $rules["slogan.$code"]      = ['nullable', 'string', 'max:255'];
            $rules["description.$code"] = ['nullable', 'string'];
        }

        return $rules;
    }

    public function prepareForValidation(): void
    {
        // Normalize booleans
        $this->merge([
            'is_published'   => (bool) $this->boolean('is_published'),
            'is_link_active' => (bool) $this->boolean('is_link_active'),
            'remove_logo'    => (bool) $this->boolean('remove_logo'),
            'remove_banner'  => (bool) $this->boolean('remove_banner'),
        ]);

        // Ensure arrays exist
        $name        = (array) $this->input('name', []);
        $slug        = (array) $this->input('slug', []);
        $slogan      = (array) $this->input('slogan', []);
        $description = (array) $this->input('description', []);

        $locales = array_keys(config('app.locales', ['hr' => 'Hrvatski', 'en' => 'English']));

        // HR -> EN fallback ako EN prazno
        if (in_array('hr', $locales, true) && in_array('en', $locales, true)) {
            $this->hrToEnFallback($name);
            $this->hrToEnFallback($slug);
            $this->hrToEnFallback($slogan);
            $this->hrToEnFallback($description);
        }

        // Auto-slug per locale (ako i dalje prazan)
        foreach ($locales as $code) {
            if (!empty($name[$code]) && empty($slug[$code])) {
                $slug[$code] = Str::slug($name[$code], '-', $code);
            }
        }

        // ---- NOVO: weburl normalizacija ----
        $weburl = trim((string) $this->input('weburl', ''));
        if ($weburl !== '') {
            // Ako nema shemu (http/https) → dodaj http://
            if (!Str::startsWith(Str::lower($weburl), ['http://', 'https://'])) {
                $weburl = preg_replace('#^/*#', '', $weburl) ?? $weburl;
                $weburl = 'http://' . $weburl;
            }

            // Uredi host u lowercase i ukloni duple kose crte u pathu
            try {
                $parts = parse_url($weburl);
                if ($parts !== false) {
                    $scheme = $parts['scheme'] ?? 'http';
                    $host   = $parts['host'] ?? '';
                    $path   = $parts['path'] ?? '';
                    $path   = '/' . ltrim($path, '/');
                    if ($path === '/') { $path = ''; }
                    $query  = isset($parts['query']) ? '?' . $parts['query'] : '';
                    $frag   = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
                    $weburl = $scheme . '://' . Str::lower($host) . $path . $query . $frag;
                }
            } catch (\Throwable $e) {
                // Ako parse padne, ostavi kakvo jest; validacija će uhvatiti krivi unos.
            }
        }
        // -------------------------------

        $this->merge([
            'name'        => $name,
            'slug'        => $slug,
            'slogan'      => $slogan,
            'description' => $description,
            'weburl'      => $weburl,
        ]);
    }

    protected function hrToEnFallback(array &$bag): void
    {
        if (blank($bag['en'] ?? null) && filled($bag['hr'] ?? null)) {
            $bag['en'] = $bag['hr'];
        }
    }

    public function attributes(): array
    {
        $attrs = [
            'level_id'        => 'level',
            'oib'             => 'OIB',
            'street'          => 'street',
            'street_no'       => 'street number',
            'city'            => 'city',
            'state'           => 'state',
            'email'           => 'email',
            'weburl'          => 'web address',
            'phone'           => 'phone',
            'is_published'    => 'published',
            'is_link_active'  => 'link active',
            'referrals_count' => 'referrals count',
            'clicks'          => 'clicks',
            'published_at'    => 'published at',
        ];

        foreach (array_keys(config('app.locales', [])) as $code) {
            $up = strtoupper($code);
            $attrs["name.$code"] = "name ($up)";
            $attrs["slug.$code"] = "slug ($up)";
            $attrs["slogan.$code"] = "slogan ($up)";
            $attrs["description.$code"] = "description ($up)";
        }

        return $attrs;
    }

    // Helperi za controller (opcionalno)
    public function baseData(): array
    {
        return Arr::only($this->validated(), [
            'level_id','oib','street','street_no','city','state','email','weburl','phone',
            'is_published','is_link_active','referrals_count','clicks','published_at',
        ]);
    }

    public function translationsData(): array
    {
        $v = $this->validated();
        $locales = array_keys(config('app.locales', []));
        $out = [];
        foreach ($locales as $code) {
            $out[$code] = [
                'locale'      => $code,
                'name'        => $v['name'][$code] ?? null,
                'slug'        => $v['slug'][$code] ?? null,
                'slogan'      => $v['slogan'][$code] ?? null,
                'description' => $v['description'][$code] ?? null,
            ];
        }
        return $out;
    }
}
