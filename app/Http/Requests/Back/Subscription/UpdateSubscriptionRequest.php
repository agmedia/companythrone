<?php

namespace App\Http\Requests\Back\Subscription;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'plan'   => ['required','string','max:50'],
            'period' => ['required', Rule::in(['monthly','yearly'])],
            'price'  => ['required','numeric','min:0','max:999999.99'],
            'currency' => ['required','string','size:3'],

            'status' => ['required', Rule::in(['trialing','active','paused','canceled','expired'])],
            'is_auto_renew' => ['boolean'],

            'starts_on'       => ['nullable','date'],
            'ends_on'         => ['nullable','date','after_or_equal:starts_on'],
            'trial_ends_on'   => ['nullable','date','after_or_equal:starts_on'],
            'next_renewal_on' => ['nullable','date'],
            'canceled_at'     => ['nullable','date'],

            'notes' => ['nullable','string'],
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'is_auto_renew' => (bool) $this->boolean('is_auto_renew'),
        ]);
    }
}
