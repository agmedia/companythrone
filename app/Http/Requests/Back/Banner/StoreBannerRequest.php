<?php

namespace App\Http\Requests\Back\Banner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    /**
     * Ako je EN prazan, popuni ga s HR prije validacije.
     */
    protected function prepareForValidation(): void
    {
        $tr = $this->input('tr', []);

        // normaliziraj locale ključeve (mala slova) i trimaj stringove
        $norm = [];
        foreach ($tr as $loc => $data) {
            $loc = strtolower((string)$loc);
            $norm[$loc] = is_array($data)
                ? array_map(fn($v) => is_string($v) ? trim($v) : $v, $data)
                : [];
        }

        $hr = $norm['hr'] ?? [];
        $en = $norm['en'] ?? [];

        // po polju: ako je EN prazno, upiši HR vrijednost
        foreach (['title','slogan','url'] as $f) {
            $enVal = $en[$f] ?? null;
            $hrVal = $hr[$f] ?? null;
            if (($enVal === null || $enVal === '') && ($hrVal !== null && $hrVal !== '')) {
                $en[$f] = $hrVal;
            }
        }

        $norm['en'] = $en;
        $this->merge(['tr' => $norm]);
    }


    public function rules(): array
    {
        $statuses = ['draft','active','archived'];

        return [
            'status' => ['required', Rule::in($statuses)],
            'image'  => ['nullable','image','mimes:jpg,jpeg,png,webp,avif','max:8192'],

            // translations: tr[hr][title], tr[hr][slogan], tr[hr][url]...
            'tr'          => ['required','array'],
            'tr.*.title'  => ['required','string','max:255'],
            'tr.*.slogan' => ['nullable','string','max:255'],
            'tr.*.url'    => ['nullable','url','max:1024'],
        ];
    }
}
