<?php

namespace App\Http\Requests\Back\Banner;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'start'    => ['required','date'], // FullCalendar ISO ili YYYY-MM-DD
            'end'      => ['nullable','date','after_or_equal:start'],
            'position' => ['required','integer','min:1','max:10'],
        ];
    }
}
