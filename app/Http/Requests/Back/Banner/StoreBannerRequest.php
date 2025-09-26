<?php

namespace App\Http\Requests\Back\Banner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $statuses = ['draft','active','archived'];

        return [
            'status' => ['required', Rule::in($statuses)],
            'image'  => ['nullable','image','mimes:jpg,jpeg,png,webp,avif','max:8192'],

            // translations: tr[hr][title], tr[hr][slogan], tr[hr][url]...
            'tr'                 => ['required','array'],
            'tr.*.title'         => ['required','string','max:255'],
            'tr.*.slogan'        => ['nullable','string','max:255'],
            'tr.*.url'           => ['nullable','url','max:1024'],
        ];
    }
}
