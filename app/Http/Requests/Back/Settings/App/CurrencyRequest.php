<?php

namespace App\Http\Requests\Back\Settings\App;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { return true; }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data.id'              => ['nullable','integer','min:0'],
            'data.title'           => ['required','array','min:1'],
            'data.code'            => ['required','string','max:8'],
            'data.symbol_left'     => ['nullable','string','max:8'],
            'data.symbol_right'    => ['nullable','string','max:8'],
            'data.value'           => ['required','numeric','gt:0'],
            'data.decimal_places'  => ['required','integer','min:0','max:6'],
            'data.status'          => ['required','boolean'],
            'data.main'            => ['required','boolean'],
        ];
    }
}
