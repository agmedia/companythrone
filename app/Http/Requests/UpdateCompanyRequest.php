<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('company')?->id;

        return [
            'name'  => ['required','string','max:255'],
            'oib'   => ['required','string','max:20', Rule::unique('companies','oib')->ignore($id)],
            'email' => ['required','email','max:255', Rule::unique('companies','email')->ignore($id)],
            'logo'  => ['nullable','image','mimes:png,jpg,jpeg,webp,svg','max:2048'],
        ];
    }
}
