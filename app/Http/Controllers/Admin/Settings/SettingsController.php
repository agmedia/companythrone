<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Back\Settings\UpdateSettingsRequest;
use App\Services\Settings\SettingsManager;
use Illuminate\Http\Request;

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
        $data = $request->normalized(); // castano po tipovima
        $this->settings->setMany($data);

        return back()->with('success', 'Settings saved.');
    }
}
