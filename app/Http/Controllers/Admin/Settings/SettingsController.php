<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\Settings\UpdateSettingsRequest;
use App\Services\Settings\SettingsManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct(private SettingsManager $settings) {}

    public function index(Request $request)
    {
        $groups = config('settings.groups', []);
        $fields = config('settings.fields', []);

        // Pročitamo trenutne vrijednosti iz baze
        $values = [];
        foreach ($fields as $code => $defs) {
            foreach ($defs as $key => $def) {
                $default = $def['default'] ?? null;
                $values[$code][$key] = $this->settings->get($code, $key, $default);
            }
        }

        return view('admin.settings.index', compact('groups','fields','values'));
    }



    public function update(UpdateSettingsRequest $request)
    {
        // Sve “normalne” vrijednosti (brojevi, bool, i18n...) – kao i do sada
        $data = $request->normalized();

        // Dohvatimo konfiguraciju polja (tipove, itd.)
        $fieldsConfig = config('settings.fields', []);

        foreach ($fieldsConfig as $group => $fields) {
            foreach ($fields as $key => $def) {
                $type = $def['type'] ?? 'text';

                if ($type === 'file-input') {
                    $inputName = "settings.$group.$key";

                    // Ako je uploadan novi file
                    if ($request->hasFile($inputName)) {
                        $file = $request->file($inputName);

                        // (opcionalno) obriši staru sliku
                        $old = $this->settings->get($group, $key, null);
                        if ($old && Storage::disk('public')->exists($old)) {
                            Storage::disk('public')->delete($old);
                        }

                        // spremi novu
                        $path = $file->store("uploads/settings/$group", 'public');

                        // upiši path u $data da ga setMany spremi
                        data_set($data, "$group.$key", $path);
                    } else {
                        // Ako nema novog uploada – zadrži postojeću vrijednost iz DB-a
                        // osim ako ju je normalized() već eksplicitno postavio
                        if (data_get($data, "$group.$key") === null) {
                            $existing = $this->settings->get($group, $key, null);
                            if ($existing !== null) {
                                data_set($data, "$group.$key", $existing);
                            }
                        }
                    }
                }
            }
        }

        // Sve skupa spremimo kroz SettingsManager kao i prije
        $this->settings->setMany($data);

        return back()->with('success', 'Settings saved.');
    }

}
