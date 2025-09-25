<?php

namespace App\Http\Requests\Back\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // dopuštamo sve, ali po tipu ćemo kasnije normalizirati
        return [
            'settings' => ['required','array'],
        ];
    }

    public function normalized(): array
    {
        // Prođi kroz config/settings.php i castaj po tipu
        $cfg = config('settings.fields', []);
        $incoming = $this->input('settings', []);
        $out = [];

        foreach ($incoming as $code => $pairs) {
            foreach (($pairs ?? []) as $key => $val) {
                $type = $cfg[$code][$key]['type'] ?? 'text';

                switch ($type) {
                    case 'boolean':
                        $val = filter_var($val, FILTER_VALIDATE_BOOL);
                        break;
                    case 'number':
                        $val = is_numeric($val) ? (int) $val : null;
                        break;
                    case 'decimal':
                        $val = is_numeric($val) ? (float) $val : null;
                        break;
                    case 'i18n_text':
                    case 'i18n_textarea':
                        // očekujemo array po locale => string
                        $val = is_array($val) ? $val : [];
                        break;
                    default:
                        $val = is_string($val) ? $val : '';
                }

                $out[$code][$key] = $val;
            }
        }

        return $out;
    }
}
