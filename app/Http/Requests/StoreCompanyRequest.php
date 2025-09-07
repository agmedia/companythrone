<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'      => ['required','string','max:255'],
            'oib'       => ['required','string','max:20','unique:companies,oib'],
            'email'     => ['required','email','max:255','unique:companies,email'],
            'street'    => ['nullable','string','max:255'],
            'street_no' => ['nullable','string','max:50'],
            'city'      => ['nullable','string','max:255'],
            'state'     => ['nullable','string','max:255'],
            'phone'     => ['nullable','string','max:50'],
            'logo'      => ['nullable','image','mimes:png,jpg,jpeg,webp,svg','max:2048'],
            'categories'=> ['nullable','array'],
            'categories.*' => ['integer','exists:categories,id'],
        ];
    }
}
